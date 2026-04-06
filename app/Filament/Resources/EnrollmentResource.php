<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Filament\Resources\EnrollmentResource\RelationManagers;
use App\Models\Enrollment;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Enrollment Management';
    
    protected static ?string $navigationLabel = 'Student Registration';
    
    protected static ?string $modelLabel = 'Registration';
    
    protected static ?string $pluralModelLabel = 'Registrations';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperadmin() || $user->isAdmin());
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Registration Details')
                    ->description('Select student and program for registration')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Student')
                            ->searchable()
                            ->required()
                            ->getSearchResultsUsing(function (string $search): array {
                                return \App\Models\Student::query()
                                    ->where('status', 'ACTIVE')
                                    ->where(function ($q) use ($search) {
                                        $q->where('student_no', 'like', "%{$search}%")
                                            ->orWhere('first_name', 'like', "%{$search}%")
                                            ->orWhere('last_name', 'like', "%{$search}%");
                                    })
                                    ->orderBy('student_no')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn ($s) => [$s->id => "{$s->student_no} - {$s->full_name}"])
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                $student = \App\Models\Student::find($value);
                                return $student ? "{$student->student_no} - {$student->full_name}" : null;
                            })
                            ->placeholder('Type to search students...')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('package_id')
                            ->label('Program/Package')
                            ->options(function () {
                                return \App\Models\Package::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->live()
                            ->placeholder('Select a program')
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                if ($state) {
                                    $package = \App\Models\Package::find($state);
                                    if ($package) {
                                        $set('total_fee', $package->total_fee);
                                        $set('downpayment_percent', $package->downpayment_percent);
                                        
                                        $downpaymentAmount = ($package->total_fee * $package->downpayment_percent) / 100;
                                        $set('downpayment_amount', $downpaymentAmount);
                                        $set('remaining_balance', $package->total_fee - $downpaymentAmount);
                                    }
                                } else {
                                    // Clear fields when no package selected
                                    $set('total_fee', null);
                                    $set('downpayment_percent', null);
                                    $set('downpayment_amount', null);
                                    $set('remaining_balance', null);
                                }
                            }),

                        Forms\Components\DatePicker::make('enrollment_date')
                            ->label('Registration Date')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->maxDate(now()),

                        Forms\Components\Select::make('status')
                            ->options([
                                'ACTIVE' => 'Active',
                                'CANCELLED' => 'Cancelled',
                            ])
                            ->default('ACTIVE')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Payment Plan Summary')
                    ->description('Calculated payment breakdown')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\TextInput::make('total_fee')
                            ->label('Total Program Fee')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('0.00')
                            ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '0.00'),

                        Forms\Components\TextInput::make('downpayment_percent')
                            ->label('Down Payment %')
                            ->numeric()
                            ->suffix('%')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('0.00')
                            ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '0.00'),

                        Forms\Components\TextInput::make('downpayment_amount')
                            ->label('Down Payment Amount')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('0.00')
                            ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '0.00'),

                        Forms\Components\TextInput::make('remaining_balance')
                            ->label('Balance to Pay')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('0.00')
                            ->formatStateUsing(fn ($state) => $state ? number_format($state, 2) : '0.00'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Session Tracking')
                    ->description('Track student attendance sessions')
                    ->icon('heroicon-o-calendar-days')
                    ->schema([
                        Forms\Components\TextInput::make('total_sessions')
                            ->label('Total Sessions Included')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Total number of sessions included in this enrollment')
                            ->required(),

                        Forms\Components\TextInput::make('sessions_used')
                            ->label('Sessions Used')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('Automatically updated when attendance is marked'),

                        Forms\Components\Placeholder::make('session_info')
                            ->label('Remaining Sessions')
                            ->content(function (Forms\Get $get) {
                                $total = $get('total_sessions') ?? 0;
                                $used = $get('sessions_used') ?? 0;
                                $remaining = max(0, $total - $used);
                                
                                if ($total == 0) {
                                    return 'No sessions configured';
                                }
                                
                                return "{$remaining} sessions remaining out of {$total} total";
                            }),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['student', 'package']))
            ->columns([
                Tables\Columns\TextColumn::make('student.student_no')
                    ->label('Student No')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-identification')
                    ->color('primary')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name'])
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('package.name')
                    ->label('Program')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Registration Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('total_fee')
                    ->label('Total Fee')
                    ->money('PHP')
                    ->sortable()
                    ->alignEnd()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('remaining_balance')
                    ->label('Balance')
                    ->money('PHP')
                    ->sortable()
                    ->alignEnd()
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                    ->weight('semibold')
                    ->formatStateUsing(fn ($state) => $state <= 0 ? 'Fully Paid' : '₱' . number_format($state, 2)),

                Tables\Columns\TextColumn::make('sessions_remaining')
                    ->label('Sessions')
                    ->badge()
                    ->formatStateUsing(fn ($record) => "{$record->sessions_used} / {$record->total_sessions}")
                    ->description(fn ($record) => $record->total_sessions > 0 ? "{$record->sessions_remaining} remaining" : 'No sessions')
                    ->color(fn ($record) => match(true) {
                        $record->total_sessions == 0 => 'gray',
                        ($record->sessions_used / max(1, $record->total_sessions)) < 0.5 => 'success',
                        ($record->sessions_used / max(1, $record->total_sessions)) < 0.8 => 'warning',
                        default => 'danger'
                    })
                    ->icon(fn ($record) => match(true) {
                        $record->total_sessions == 0 => 'heroicon-o-minus-circle',
                        $record->sessions_remaining > 0 => 'heroicon-o-check-circle',
                        default => 'heroicon-o-x-circle'
                    })
                    ->sortable(['sessions_used'])
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'ACTIVE',
                        'danger' => 'CANCELLED',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'ACTIVE',
                        'heroicon-o-x-circle' => 'CANCELLED',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ACTIVE' => 'Active',
                        'CANCELLED' => 'Cancelled',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No registrations yet')
            ->emptyStateDescription('Start by registering a student to a program.')
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Register Student')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultPaginationPageOption(25)
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->deferLoading()
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentSchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'view' => Pages\ViewEnrollment::route('/{record}'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}

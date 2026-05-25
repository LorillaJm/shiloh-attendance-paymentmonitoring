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

    protected static ?string $navigationIcon = 'heroicon-m-academic-cap';

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
        $user = auth()->user();
        return $user && ($user->isSuperadmin() || $user->isAdmin());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Registration Details')
                    ->description('Select student and program for registration')
                    ->icon('heroicon-m-academic-cap')
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
                            ->searchable()
                            ->required()
                            ->live()
                            ->preload()
                            ->placeholder('Select a program')
                            ->helperText('If no programs appear, please create a package first in Master Data > Packages')
                            ->rules([
                                fn (Forms\Get $get, ?Enrollment $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                    $studentId = $get('student_id');
                                    if (!$studentId || !$value) return;

                                    $exists = Enrollment::query()
                                        ->where('student_id', $studentId)
                                        ->where('package_id', $value)
                                        ->where('status', 'ACTIVE')
                                        ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                        ->exists();

                                    if ($exists) {
                                        $fail('This student already has an active registration for this program.');
                                    }
                                },
                            ])
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                if ($state) {
                                    $package = \App\Models\Package::find($state);
                                    if ($package) {
                                        $set('total_fee', $package->total_fee);
                                        $set('downpayment_amount', 0);
                                        $set('remaining_balance', $package->total_fee);
                                    }
                                } else {
                                    // Clear fields when no package selected
                                    $set('total_fee', null);
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
                    ->description('Enter payment details manually')
                    ->icon('heroicon-m-banknotes')
                    ->schema([
                        Forms\Components\TextInput::make('total_fee')
                            ->label('Total Program Fee')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->live(onBlur: true)
                            ->placeholder('0.00')
                            ->helperText('Enter the program fee (can be adjusted for discounts)')
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                $totalFee = (float) ($state ?? 0);
                                $downpayment = (float) ($get('downpayment_amount') ?? 0);
                                $set('remaining_balance', max(0, $totalFee - $downpayment));
                            }),

                        Forms\Components\TextInput::make('downpayment_amount')
                            ->label('Down Payment Amount')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->placeholder('0.00')
                            ->helperText('Enter the down payment amount directly')
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                                $totalFee = (float) ($get('total_fee') ?? 0);
                                $downpayment = (float) ($state ?? 0);
                                $set('remaining_balance', max(0, $totalFee - $downpayment));
                                // Calculate downpayment_percent for database
                                $percent = $totalFee > 0 ? ($downpayment / $totalFee) * 100 : 0;
                                $set('downpayment_percent', round($percent, 2));
                            }),

                        // Hidden field to store calculated percentage (required by database)
                        Forms\Components\Hidden::make('downpayment_percent')
                            ->default(0),

                        Forms\Components\TextInput::make('remaining_balance')
                            ->label('Balance to Pay')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->placeholder('0.00')
                            ->helperText('Auto-calculated: Total Fee - Down Payment'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Session Tracking')
                    ->description('Track student attendance sessions')
                    ->icon('heroicon-m-calendar-days')
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
                            ->minValue(0)
                            ->live(onBlur: true)
                            ->helperText('Enter sessions used manually or auto-updated via attendance'),

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
                    ->icon('heroicon-m-identification')
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
                    ->icon('heroicon-m-calendar'),

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
                        $record->total_sessions == 0 => 'heroicon-m-minus-circle',
                        $record->sessions_remaining > 0 => 'heroicon-m-check-circle',
                        default => 'heroicon-m-x-circle'
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
                        'heroicon-m-check-circle' => 'ACTIVE',
                        'heroicon-m-x-circle' => 'CANCELLED',
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
            ->emptyStateIcon('heroicon-m-academic-cap')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Register Student')
                    ->icon('heroicon-m-plus'),
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

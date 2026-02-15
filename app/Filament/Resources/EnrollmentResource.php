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
        return auth()->user()?->isAdmin() ?? false;
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
                            ->relationship('student', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->student_no} - {$record->full_name}")
                            ->searchable(['student_no', 'first_name', 'last_name'])
                            ->required()
                            ->preload()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('package_id')
                            ->label('Program/Package')
                            ->relationship('package', 'name')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    $package = Package::find($state);
                                    if ($package) {
                                        $set('total_fee', $package->total_fee);
                                        $set('downpayment_percent', $package->downpayment_percent);
                                        
                                        $downpaymentAmount = ($package->total_fee * $package->downpayment_percent) / 100;
                                        $set('downpayment_amount', $downpaymentAmount);
                                        $set('remaining_balance', $package->total_fee - $downpaymentAmount);
                                    }
                                }
                            })
                            ->preload(),

                        Forms\Components\DatePicker::make('enrollment_date')
                            ->label('Registration Date')
                            ->required()
                            ->default(now())
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->options([
                                'ACTIVE' => 'Active',
                                'CANCELLED' => 'Cancelled',
                            ])
                            ->default('ACTIVE')
                            ->required(),
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
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('downpayment_percent')
                            ->label('Down Payment %')
                            ->numeric()
                            ->suffix('%')
                            ->required()
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('downpayment_amount')
                            ->label('Down Payment Amount')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->readOnly()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('remaining_balance')
                            ->label('Balance to Pay')
                            ->numeric()
                            ->prefix('₱')
                            ->required()
                            ->readOnly()
                            ->dehydrated(),
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
            ->striped()
            ->poll('30s');
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

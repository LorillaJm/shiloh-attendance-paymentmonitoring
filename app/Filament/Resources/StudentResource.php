<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use App\Enums\StudentStatus;
use App\Enums\Sex;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Students';

    protected static ?string $recordTitleAttribute = 'full_name';

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
                Forms\Components\Section::make('Personal Information')
                    ->description('Basic student details and identification')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\TextInput::make('student_no')
                            ->label('Student Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated')
                            ->helperText('Will be auto-generated as SHILOH-YYYY-0001')
                            ->visible(fn ($context) => $context === 'edit')
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->regex('/^[a-zA-Z\s\-\'\.]+$/')
                            ->validationMessages([
                                'regex' => 'First name must contain only letters, spaces, hyphens, apostrophes, and periods.',
                            ])
                            ->placeholder('Juan')
                            ->autofocus(),
                        
                        Forms\Components\TextInput::make('middle_name')
                            ->minLength(2)
                            ->maxLength(255)
                            ->regex('/^[a-zA-Z\s\-\'\.]+$/')
                            ->validationMessages([
                                'regex' => 'Middle name must contain only letters, spaces, hyphens, apostrophes, and periods.',
                            ])
                            ->placeholder('Santos'),
                        
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->regex('/^[a-zA-Z\s\-\'\.]+$/')
                            ->validationMessages([
                                'regex' => 'Last name must contain only letters, spaces, hyphens, apostrophes, and periods.',
                            ])
                            ->placeholder('Dela Cruz'),
                        
                        Forms\Components\DatePicker::make('birthdate')
                            ->native(false)
                            ->displayFormat('M d, Y')
                            ->maxDate(now()->subYears(3))
                            ->minDate(now()->subYears(25))
                            ->helperText('Student must be between 3 and 25 years old')
                            ->placeholder('Select birthdate'),
                        
                        Forms\Components\Select::make('sex')
                            ->options(Sex::options())
                            ->native(false)
                            ->placeholder('Select sex'),
                        
                        Forms\Components\Select::make('status')
                            ->options(StudentStatus::options())
                            ->default('ACTIVE')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(3)
                    ->collapsible(),
                
                Forms\Components\Section::make('Contact & Guardian Information')
                    ->description('Address and guardian details')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->minLength(10)
                            ->maxLength(65535)
                            ->placeholder('Complete address')
                            ->helperText('Minimum 10 characters')
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('guardian_name')
                            ->label('Guardian Name')
                            ->required()
                            ->minLength(2)
                            ->maxLength(255)
                            ->regex('/^[a-zA-Z\s\-\'\.]+$/')
                            ->validationMessages([
                                'regex' => 'Guardian name must contain only letters, spaces, hyphens, apostrophes, and periods.',
                            ])
                            ->placeholder('Parent/Guardian full name'),
                        
                        Forms\Components\TextInput::make('guardian_contact')
                            ->label('Guardian Contact')
                            ->required()
                            ->tel()
                            ->maxLength(255)
                            ->regex('/^(\+63|0)[0-9]{10}$/')
                            ->validationMessages([
                                'regex' => 'Please enter a valid Philippine phone number (e.g., +639123456789 or 09123456789).',
                            ])
                            ->placeholder('+63 912 345 6789'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student_no')
                    ->label('Student No.')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-identification')
                    ->weight('semibold')
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable(['first_name', 'last_name', 'middle_name'])
                    ->sortable(['last_name', 'first_name'])
                    ->description(fn (Student $record): string => $record->guardian_name ? "Guardian: {$record->guardian_name}" : '')
                    ->weight('medium'),
                
                Tables\Columns\TextColumn::make('birthdate')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-cake'),
                
                Tables\Columns\TextColumn::make('sex')
                    ->badge()
                    ->colors([
                        'blue' => 'Male',
                        'pink' => 'Female',
                    ])
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('guardian_contact')
                    ->label('Contact')
                    ->toggleable()
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'ACTIVE',
                        'gray' => 'INACTIVE',
                        'danger' => 'DROPPED',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'ACTIVE',
                        'heroicon-o-pause-circle' => 'INACTIVE',
                        'heroicon-o-x-circle' => 'DROPPED',
                    ])
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Enrolled')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(StudentStatus::options())
                    ->native(false)
                    ->multiple(),
                
                SelectFilter::make('sex')
                    ->options(Sex::options())
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No students found')
            ->emptyStateDescription('Add your first student to get started with attendance and payments.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Student')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('student_no', 'desc')
            ->defaultPaginationPageOption(25)
            ->deferLoading()
            ->striped()
            ->poll('30s');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'ACTIVE')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}

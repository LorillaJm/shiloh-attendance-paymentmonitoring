<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuardianResource\Pages;
use App\Models\Guardian;
use App\Models\User;
use App\Enums\UserRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class GuardianResource extends Resource
{
    protected static ?string $model = Guardian::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Account')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('User Account')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('email')->email()->required()->unique('users', 'email'),
                                Forms\Components\TextInput::make('password')->password()->required()->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                                Forms\Components\Hidden::make('role')->default(UserRole::PARENT->value),
                            ])
                            ->required(),
                    ]),
                Forms\Components\Section::make('Guardian Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')->required(),
                        Forms\Components\TextInput::make('last_name')->required(),
                        Forms\Components\TextInput::make('middle_name'),
                        Forms\Components\TextInput::make('contact_number')->required()->tel(),
                        Forms\Components\TextInput::make('email')->email(),
                        Forms\Components\Textarea::make('address')->rows(2),
                        Forms\Components\TextInput::make('relationship')->placeholder('Mother, Father, Guardian, etc.'),
                    ])->columns(2),
                Forms\Components\Section::make('Students')
                    ->schema([
                        Forms\Components\Repeater::make('students')
                            ->relationship('students')
                            ->schema([
                                Forms\Components\Select::make('id')
                                    ->label('Student')
                                    ->relationship('students', 'first_name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\Toggle::make('is_primary')
                                    ->label('Primary Guardian')
                                    ->default(false),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->label('Name')->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('contact_number')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('relationship'),
                Tables\Columns\TextColumn::make('students_count')->counts('students')->label('Students'),
                Tables\Columns\TextColumn::make('user.email')->label('Portal Email'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuardians::route('/'),
            'create' => Pages\CreateGuardian::route('/create'),
            'edit' => Pages\EditGuardian::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }
}

<?php

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-m-users';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 1;
    
    // Apply UserPolicy to all actions
    protected static ?string $modelPolicy = \App\Policies\UserPolicy::class;

    public static function shouldRegisterNavigation(): bool
    {
        // Only show to superadmin
        return auth()->user()?->isSuperadmin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Name is required.',
                                'max' => 'Name cannot exceed 255 characters.',
                            ]),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Email address is required.',
                                'email' => 'Please enter a valid email address.',
                                'unique' => 'This email address is already registered.',
                            ]),
                        Forms\Components\Select::make('role')
                            ->options(function () {
                                $user = auth()->user();
                                
                                // Only superadmin can see and assign SUPERADMIN role
                                if ($user?->isSuperadmin()) {
                                    return [
                                        UserRole::SUPERADMIN->value => UserRole::SUPERADMIN->label(),
                                        UserRole::ADMIN->value => UserRole::ADMIN->label(),
                                        UserRole::PARENT->value => UserRole::PARENT->label(),
                                    ];
                                }
                                
                                return [
                                    UserRole::ADMIN->value => UserRole::ADMIN->label(),
                                    UserRole::PARENT->value => UserRole::PARENT->label(),
                                ];
                            })
                            ->required()
                            ->default(UserRole::ADMIN->value)
                            ->disabled(function ($record) {
                                // Prevent users from changing their own role
                                return $record && auth()->id() === $record->id;
                            })
                            ->helperText(function ($record) {
                                if ($record && auth()->id() === $record->id) {
                                    return 'You cannot change your own role.';
                                }
                                return 'Note: Parent users should be created via the Guardian resource.';
                            })
                            ->visible(fn () => auth()->user()?->isSuperadmin() ?? false)
                            ->in([UserRole::SUPERADMIN->value, UserRole::ADMIN->value, UserRole::PARENT->value])
                            ->validationMessages([
                                'required' => 'Please select a user role.',
                                'in' => 'Invalid role selected. Must be SUPERADMIN, ADMIN, or PARENT.',
                            ]),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->minLength(8)
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'Password is required for new users.',
                                'min' => 'Password must be at least 8 characters long for security.',
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'success' => UserRole::SUPERADMIN->value,
                        'danger' => UserRole::ADMIN->value,
                        'info' => UserRole::PARENT->value,
                    ])
                    ->formatStateUsing(fn (UserRole $state): string => $state->label()),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        UserRole::SUPERADMIN->value => UserRole::SUPERADMIN->label(),
                        UserRole::ADMIN->value => UserRole::ADMIN->label(),
                        UserRole::PARENT->value => UserRole::PARENT->label(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, User $record) {
                        // Prevent deleting yourself
                        if (auth()->id() === $record->id) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Cannot delete your own account')
                                ->send();
                            
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Tables\Actions\DeleteBulkAction $action, $records) {
                            // Prevent deleting yourself in bulk action
                            if ($records->contains('id', auth()->id())) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Cannot delete your own account')
                                    ->send();
                                
                                $action->cancel();
                            }
                        }),
                ]),
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}

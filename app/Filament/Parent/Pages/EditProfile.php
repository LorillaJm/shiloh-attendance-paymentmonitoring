<?php

namespace App\Filament\Parent\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->description('Update your account profile information.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Update Password')
                    ->description('Ensure your account is using a secure password.')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->columns(1),
            ]);
    }

    protected function getNameFormComponent(): Forms\Components\Component
    {
        return Forms\Components\TextInput::make('name')
            ->label('Full Name')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Forms\Components\Component
    {
        return Forms\Components\TextInput::make('email')
            ->label('Email Address')
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true);
    }

    protected function getPasswordFormComponent(): Forms\Components\Component
    {
        return Forms\Components\TextInput::make('password')
            ->label('New Password')
            ->password()
            ->revealable()
            ->rule(Password::default())
            ->autocomplete('new-password')
            ->dehydrated(fn ($state): bool => filled($state))
            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('passwordConfirmation')
            ->helperText('Leave blank to keep current password');
    }

    protected function getPasswordConfirmationFormComponent(): Forms\Components\Component
    {
        return Forms\Components\TextInput::make('passwordConfirmation')
            ->label('Confirm New Password')
            ->password()
            ->revealable()
            ->required(fn (Forms\Get $get): bool => filled($get('password')))
            ->dehydrated(false)
            ->helperText('Re-enter your new password to confirm');
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Profile updated')
            ->body('Your changes have been saved successfully.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): ?string
    {
        return url()->previous();
    }
}

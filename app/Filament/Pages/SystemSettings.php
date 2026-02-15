<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SystemSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.system-settings';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Settings';

    public static function shouldRegisterNavigation(): bool
    {
        // Only show to admins
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        // Only admins can access
        return auth()->user()?->isAdmin() ?? false;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'school_name' => SystemSetting::get('school_name'),
            'school_address' => SystemSetting::get('school_address'),
            'school_phone' => SystemSetting::get('school_phone'),
            'school_email' => SystemSetting::get('school_email'),
            'school_logo' => SystemSetting::get('school_logo'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('School Information')
                    ->schema([
                        TextInput::make('school_name')
                            ->label('School Name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('school_address')
                            ->label('Address')
                            ->rows(3)
                            ->maxLength(500),
                        TextInput::make('school_phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(50),
                        TextInput::make('school_email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255),
                        FileUpload::make('school_logo')
                            ->label('School Logo')
                            ->image()
                            ->directory('logos')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText('Upload school logo (max 2MB, recommended: 200x200px)'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SystemSetting::set($key, $value);
        }

        Notification::make()
            ->success()
            ->title('Settings saved successfully')
            ->send();
    }
}

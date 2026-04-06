<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Notification;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Announcements';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Announcement Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('target_audience')
                            ->label('Send To')
                            ->options([
                                'all' => 'All Users',
                                'parents' => 'All Parents',
                                'admins' => 'All Admins',
                                'specific_user' => 'Specific User',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => 
                                $state !== 'specific_user' ? $set('target_user_id', null) : null
                            ),

                        Forms\Components\Select::make('target_user_id')
                            ->label('Select User')
                            ->options(fn () => User::all()->mapWithKeys(function ($user) {
                                try {
                                    $roleLabel = $user->role->value ?? 'Unknown';
                                } catch (\ValueError $e) {
                                    // Handle old/invalid role values
                                    $roleLabel = 'Unknown Role';
                                }
                                return [$user->id => "{$user->name} ({$user->email}) - {$roleLabel}"];
                            }))
                            ->searchable()
                            ->visible(fn (Forms\Get $get) => $get('target_audience') === 'specific_user')
                            ->required(fn (Forms\Get $get) => $get('target_audience') === 'specific_user'),

                        Forms\Components\Toggle::make('is_published')
                            ->label('Publish Immediately')
                            ->helperText('When enabled, notification will be sent immediately to target users')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('message')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('target_audience')
                    ->label('Sent To')
                    ->colors([
                        'primary' => 'all',
                        'success' => 'parents',
                        'warning' => 'admins',
                        'info' => 'specific_user',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'all' => 'All Users',
                        'parents' => 'All Parents',
                        'admins' => 'All Admins',
                        'specific_user' => 'Specific User',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('targetUser.name')
                    ->label('Target User')
                    ->placeholder(fn ($record) => match($record->target_audience) {
                        'all' => 'All Users',
                        'parents' => 'All Parents',
                        'admins' => 'All Admins',
                        default => '-',
                    }),

                Tables\Columns\IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published'),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Not published'),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('target_audience')
                    ->options([
                        'all' => 'All Users',
                        'parents' => 'All Parents',
                        'admins' => 'All Admins',
                        'specific_user' => 'Specific User',
                    ]),

                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('publish')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (Announcement $record) => !$record->is_published)
                    ->requiresConfirmation()
                    ->action(function (Announcement $record) {
                        $record->update([
                            'is_published' => true,
                            'published_at' => now(),
                        ]);

                        // Send notifications
                        static::sendNotifications($record);

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Announcement Published')
                            ->body('Notifications sent to target users.')
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }

    public static function sendNotifications(Announcement $announcement): void
    {
        $users = collect();

        switch ($announcement->target_audience) {
            case 'all':
                $users = User::all();
                break;
            case 'parents':
                $users = User::where('role', 'PARENT')->get();
                break;
            case 'admins':
                $users = User::whereIn('role', ['ADMIN', 'SUPERADMIN'])->get();
                break;
            case 'specific_user':
                if ($announcement->target_user_id) {
                    $users = User::where('id', $announcement->target_user_id)->get();
                }
                break;
        }

        Notification::send($users, new AnnouncementNotification($announcement));
    }
}

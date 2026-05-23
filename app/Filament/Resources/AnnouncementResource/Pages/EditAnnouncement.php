<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('publish')
                ->icon('heroicon-m-paper-airplane')
                ->color('success')
                ->visible(fn () => !$this->record->is_published)
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'is_published' => true,
                        'published_at' => now(),
                    ]);

                    AnnouncementResource::sendNotifications($this->record);

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Announcement Published')
                        ->body('Notifications sent to target users.')
                        ->send();
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['is_published'] && !$this->record->is_published) {
            $data['published_at'] = now();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->is_published && $this->record->wasChanged('is_published')) {
            AnnouncementResource::sendNotifications($this->record);
            
            \Filament\Notifications\Notification::make()
                ->success()
                ->title('Announcement Published')
                ->body('Notifications sent to target users.')
                ->send();
        }
    }
}

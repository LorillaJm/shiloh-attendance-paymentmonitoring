<?php

namespace App\Filament\Resources\AnnouncementResource\Pages;

use App\Filament\Resources\AnnouncementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        
        if ($data['is_published']) {
            $data['published_at'] = now();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->is_published) {
            AnnouncementResource::sendNotifications($this->record);
            
            \Filament\Notifications\Notification::make()
                ->success()
                ->title('Announcement Created & Published')
                ->body('Notifications sent to target users.')
                ->send();
        }
    }
}

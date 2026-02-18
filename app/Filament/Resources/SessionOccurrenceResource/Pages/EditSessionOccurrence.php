<?php

namespace App\Filament\Resources\SessionOccurrenceResource\Pages;

use App\Filament\Resources\SessionOccurrenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSessionOccurrence extends EditRecord
{
    protected static string $resource = SessionOccurrenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

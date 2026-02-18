<?php

namespace App\Filament\Resources\SessionTypeResource\Pages;

use App\Filament\Resources\SessionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSessionType extends EditRecord
{
    protected static string $resource = SessionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

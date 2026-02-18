<?php

namespace App\Filament\Resources\SessionTypeResource\Pages;

use App\Filament\Resources\SessionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSessionTypes extends ListRecords
{
    protected static string $resource = SessionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\StudentScheduleResource\Pages;

use App\Filament\Resources\StudentScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentSchedules extends ListRecords
{
    protected static string $resource = StudentScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

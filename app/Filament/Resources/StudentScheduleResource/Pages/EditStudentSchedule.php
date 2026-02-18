<?php

namespace App\Filament\Resources\StudentScheduleResource\Pages;

use App\Filament\Resources\StudentScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentSchedule extends EditRecord
{
    protected static string $resource = StudentScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

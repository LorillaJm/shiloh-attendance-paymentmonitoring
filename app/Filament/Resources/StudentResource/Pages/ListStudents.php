<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Students'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'ACTIVE'))
                ->badge(fn () => static::getModel()::where('status', 'ACTIVE')->count()),
            'inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'INACTIVE'))
                ->badge(fn () => static::getModel()::where('status', 'INACTIVE')->count()),
            'dropped' => Tab::make('Dropped')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'DROPPED'))
                ->badge(fn () => static::getModel()::where('status', 'DROPPED')->count()),
        ];
    }
}

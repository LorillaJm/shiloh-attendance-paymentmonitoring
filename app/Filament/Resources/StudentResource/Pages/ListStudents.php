<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Get student counts by status using a single aggregated query
     * Cached for 60 seconds to prevent repeated queries
     */
    protected function getStudentStatusCounts(): array
    {
        return Cache::remember('student_status_counts', 60, function () {
            // Single query with GROUP BY - much more efficient
            $counts = DB::table('students')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            
            // Calculate individual counts
            $active = $counts['ACTIVE'] ?? 0;
            $inactive = $counts['INACTIVE'] ?? 0;
            $dropped = $counts['DROPPED'] ?? 0;
            
            // Ensure all statuses have a count (even if 0)
            return [
                'ACTIVE' => $active,
                'INACTIVE' => $inactive,
                'DROPPED' => $dropped,
                'total' => $active + $inactive + $dropped,
            ];
        });
    }

    public function getTabs(): array
    {
        $counts = $this->getStudentStatusCounts();
        
        return [
            'all' => Tab::make('All Students')
                ->badge($counts['total']),
            
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'ACTIVE'))
                ->badge($counts['ACTIVE'])
                ->badgeColor('success'),
            
            'inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'INACTIVE'))
                ->badge($counts['INACTIVE'])
                ->badgeColor('gray'),
            
            'dropped' => Tab::make('Dropped')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'DROPPED'))
                ->badge($counts['DROPPED'])
                ->badgeColor('danger'),
        ];
    }
}

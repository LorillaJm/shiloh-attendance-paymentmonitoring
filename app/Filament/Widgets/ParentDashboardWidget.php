<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ParentDashboardWidget extends Widget
{
    protected static string $view = 'filament.widgets.parent-dashboard-widget';
    protected int | string | array $columnSpan = 'full';

    public function getStudents()
    {
        $guardian = Auth::user()->guardian;
        
        if (!$guardian) {
            return collect();
        }

        return $guardian->students()->with([
            'enrollments' => fn($q) => $q->where('status', 'ACTIVE'),
            'sessionOccurrences' => fn($q) => $q->where('session_date', '>=', now()->subDays(7)),
        ])->get();
    }

    public static function canView(): bool
    {
        return Auth::user()->isParent();
    }
}

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
        try {
            $user = Auth::user();
            
            // Only for parents
            if (!$user || !$user->isParent()) {
                return collect();
            }
            
            $guardian = $user->guardian;
            
            if (!$guardian) {
                return collect();
            }

            return $guardian->students()->with([
                'enrollments' => fn($q) => $q->where('status', 'ACTIVE'),
                'sessionOccurrences' => fn($q) => $q->where('session_date', '>=', now()->subDays(7)),
            ])->get();
        } catch (\Exception $e) {
            // Log error but don't break the dashboard
            \Log::error('ParentDashboardWidget error: ' . $e->getMessage());
            return collect();
        }
    }

    public static function canView(): bool
    {
        try {
            $user = Auth::user();
            return $user && $user->isParent();
        } catch (\Exception $e) {
            return false;
        }
    }
}

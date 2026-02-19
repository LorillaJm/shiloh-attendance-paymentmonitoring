<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedAlertsWidget extends Widget
{
    protected static string $view = 'filament.widgets.optimized-alerts-widget';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    // No polling
    protected static ?string $pollingInterval = null;

    public function getAlerts(): array
    {
        return Cache::remember('dashboard_alerts_v2', 300, function () {
            $today = now('Asia/Manila')->format('Y-m-d');
            $sevenDaysFromNow = now('Asia/Manila')->addDays(7)->format('Y-m-d');

            // Single query for all alert counts
            $alerts = DB::select("
                SELECT 
                    (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date < ?) as overdue_count,
                    (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date BETWEEN ? AND ?) as due_soon_count,
                    (SELECT COUNT(DISTINCT student_id) FROM students s 
                     WHERE status = 'ACTIVE' 
                     AND NOT EXISTS (
                         SELECT 1 FROM attendance_records ar 
                         WHERE ar.student_id = s.id AND DATE(ar.attendance_date) = ?
                     )) as missing_attendance_count
            ", [$today, $today, $sevenDaysFromNow, $today]);

            $data = $alerts[0];

            return [
                [
                    'title' => 'Overdue Payments',
                    'count' => $data->overdue_count,
                    'color' => 'danger',
                    'icon' => 'heroicon-o-exclamation-triangle',
                    'url' => route('filament.admin.pages.due-overdue-report'),
                ],
                [
                    'title' => 'Due Within 7 Days',
                    'count' => $data->due_soon_count,
                    'color' => 'warning',
                    'icon' => 'heroicon-o-clock',
                    'url' => route('filament.admin.pages.due-overdue-report'),
                ],
                [
                    'title' => 'Missing Attendance Today',
                    'count' => $data->missing_attendance_count,
                    'color' => 'info',
                    'icon' => 'heroicon-o-user-group',
                    'url' => route('filament.admin.pages.daily-attendance-encoder'),
                ],
            ];
        });
    }
}

<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedAlertsWidget extends Widget
{
    protected static string $view = 'filament.widgets.optimized-alerts-widget';
    
    protected static ?int $sort = 11; // After chart
    
    protected int | string | array $columnSpan = 'full';
    
    // No polling
    protected static ?string $pollingInterval = null;

    public function getAlerts(): array
    {
        try {
            return Cache::remember('dashboard_alerts_v3', 180, function () {
                $today = now('Asia/Manila')->format('Y-m-d');
                $sevenDaysFromNow = now('Asia/Manila')->addDays(7)->format('Y-m-d');

                // Single query for all alert counts
                $alerts = DB::selectOne("
                    SELECT 
                        (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date < ?) as overdue_count,
                        (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date BETWEEN ? AND ?) as due_soon_count,
                        (SELECT COUNT(*) FROM students WHERE status = 'ACTIVE' 
                         AND NOT EXISTS (
                             SELECT 1 FROM attendance_records 
                             WHERE attendance_records.student_id = students.id 
                             AND DATE(attendance_date) = ?
                         )) as missing_attendance_count
                ", [$today, $today, $sevenDaysFromNow, $today]);

                return [
                    [
                        'title' => 'Overdue Payments',
                        'count' => $alerts->overdue_count ?? 0,
                        'color' => 'danger',
                        'icon' => 'heroicon-o-exclamation-triangle',
                        'url' => route('filament.admin.pages.due-overdue-report'),
                    ],
                    [
                        'title' => 'Due Within 7 Days',
                        'count' => $alerts->due_soon_count ?? 0,
                        'color' => 'warning',
                        'icon' => 'heroicon-o-clock',
                        'url' => route('filament.admin.pages.due-overdue-report'),
                    ],
                    [
                        'title' => 'Missing Attendance Today',
                        'count' => $alerts->missing_attendance_count ?? 0,
                        'color' => 'info',
                        'icon' => 'heroicon-o-user-group',
                        'url' => route('filament.admin.pages.daily-attendance-encoder'),
                    ],
                ];
            });
        } catch (\Exception $e) {
            \Log::error('OptimizedAlertsWidget error: ' . $e->getMessage());
            return [];
        }
    }
}

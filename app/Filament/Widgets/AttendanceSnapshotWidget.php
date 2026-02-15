<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceRecord;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AttendanceSnapshotWidget extends BaseWidget
{
    protected static ?int $sort = 7;
    
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $stats = Cache::remember('dashboard_attendance_snapshot', 60, function () {
            $today = now('Asia/Manila');
            $sevenDaysAgo = $today->copy()->subDays(6); // Last 7 days including today

            // Get attendance summary for last 7 days
            $summary = AttendanceRecord::whereBetween('attendance_date', [
                    $sevenDaysAgo->format('Y-m-d'),
                    $today->format('Y-m-d')
                ])
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN status = 'PRESENT' THEN 1 ELSE 0 END) as present"),
                    DB::raw("SUM(CASE WHEN status = 'ABSENT' THEN 1 ELSE 0 END) as absent"),
                    DB::raw("SUM(CASE WHEN status = 'LATE' THEN 1 ELSE 0 END) as late"),
                    DB::raw("SUM(CASE WHEN status = 'EXCUSED' THEN 1 ELSE 0 END) as excused")
                )
                ->first();

            // Get today's attendance
            $todaySummary = AttendanceRecord::whereDate('attendance_date', $today->format('Y-m-d'))
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN status = 'PRESENT' THEN 1 ELSE 0 END) as present")
                )
                ->first();

            // Calculate attendance rate
            $attendanceRate = $summary->total > 0 
                ? round(($summary->present / $summary->total) * 100, 1)
                : 0;

            return [
                'total' => $summary->total ?? 0,
                'present' => $summary->present ?? 0,
                'absent' => $summary->absent ?? 0,
                'late' => $summary->late ?? 0,
                'excused' => $summary->excused ?? 0,
                'attendance_rate' => $attendanceRate,
                'today_total' => $todaySummary->total ?? 0,
                'today_present' => $todaySummary->present ?? 0,
            ];
        });

        return [
            Stat::make('Attendance Rate (7 Days)', $stats['attendance_rate'] . '%')
                ->description('Present / Total records')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color($stats['attendance_rate'] >= 90 ? 'success' : ($stats['attendance_rate'] >= 75 ? 'warning' : 'danger'))
                ->chart([85, 87, 89, 91, 88, 90, $stats['attendance_rate']])
                ->url(route('filament.admin.pages.daily-attendance-report')),

            Stat::make('Present (7 Days)', number_format($stats['present']))
                ->description('Students marked present')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.attendance-records.index')),

            Stat::make('Absent (7 Days)', number_format($stats['absent']))
                ->description('Students marked absent')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger')
                ->url(route('filament.admin.resources.attendance-records.index')),

            Stat::make('Today\'s Attendance', number_format($stats['today_present']) . ' / ' . number_format($stats['today_total']))
                ->description('Present / Total today')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info')
                ->url(route('filament.admin.pages.daily-attendance-encoder')),
        ];
    }
}

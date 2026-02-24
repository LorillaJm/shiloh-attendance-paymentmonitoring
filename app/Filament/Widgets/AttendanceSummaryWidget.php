<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AttendanceSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 2; // Move to top with other KPIs
    
    protected static ?string $pollingInterval = null;
    
    // Full width to display with other stats
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        try {
            $stats = Cache::remember('dashboard_attendance_summary_v1', 180, function () {
                $today = now('Asia/Manila')->format('Y-m-d');
                
                $attendance = DB::selectOne("
                    SELECT 
                        (SELECT COUNT(*) FROM attendance_records 
                         WHERE DATE(attendance_date) = ? AND status = 'PRESENT') as present_today,
                        (SELECT COUNT(*) FROM attendance_records 
                         WHERE DATE(attendance_date) = ? AND status = 'ABSENT') as absent_today,
                        (SELECT COUNT(*) FROM attendance_records 
                         WHERE DATE(attendance_date) = ? AND status = 'LATE') as late_today,
                        (SELECT COUNT(*) FROM attendance_records 
                         WHERE DATE(attendance_date) = ? AND status = 'EXCUSED') as excused_today
                ", [$today, $today, $today, $today]);

                return [
                    'present_today' => $attendance->present_today ?? 0,
                    'absent_today' => $attendance->absent_today ?? 0,
                    'late_today' => $attendance->late_today ?? 0,
                    'excused_today' => $attendance->excused_today ?? 0,
                ];
            });

            return [
                Stat::make('Present', number_format($stats['present_today']))
                    ->description('Attended')
                    ->descriptionIcon('heroicon-o-check-circle')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Absent', number_format($stats['absent_today']))
                    ->description('Did not attend')
                    ->descriptionIcon('heroicon-o-x-circle')
                    ->color('danger')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Late', number_format($stats['late_today']))
                    ->description('Arrived late')
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('warning')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Excused', number_format($stats['excused_today']))
                    ->description('With excuse')
                    ->descriptionIcon('heroicon-o-document-text')
                    ->color('info')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),
            ];
            
        } catch (\Exception $e) {
            \Log::error('AttendanceSummaryWidget error: ' . $e->getMessage());
            return [];
        }
    }
}

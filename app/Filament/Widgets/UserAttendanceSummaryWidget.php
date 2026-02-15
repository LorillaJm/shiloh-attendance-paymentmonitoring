<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceRecord;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserAttendanceSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected static ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return !auth()->user()->isAdmin();
    }

    protected function getStats(): array
    {
        $stats = Cache::remember('user_attendance_summary_' . auth()->id(), 30, function () {
            $today = now('Asia/Manila');

            // Get today's attendance summary
            $todaySummary = AttendanceRecord::whereDate('attendance_date', $today->format('Y-m-d'))
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw("SUM(CASE WHEN status = 'PRESENT' THEN 1 ELSE 0 END) as present"),
                    DB::raw("SUM(CASE WHEN status = 'ABSENT' THEN 1 ELSE 0 END) as absent"),
                    DB::raw("SUM(CASE WHEN status = 'LATE' THEN 1 ELSE 0 END) as late")
                )
                ->first();

            // Get records encoded by this user today
            $encodedToday = AttendanceRecord::whereDate('attendance_date', $today->format('Y-m-d'))
                ->where('encoded_by_user_id', auth()->id())
                ->count();

            return [
                'total' => $todaySummary->total ?? 0,
                'present' => $todaySummary->present ?? 0,
                'absent' => $todaySummary->absent ?? 0,
                'late' => $todaySummary->late ?? 0,
                'encoded_today' => $encodedToday,
            ];
        });

        return [
            Stat::make('Present Today', number_format($stats['present']))
                ->description('Students marked present')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->chart([5, 7, 8, 10, 12, 15, $stats['present']]),

            Stat::make('Absent Today', number_format($stats['absent']))
                ->description('Students marked absent')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Late Today', number_format($stats['late']))
                ->description('Students marked late')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('You Encoded', number_format($stats['encoded_today']))
                ->description('Records you entered today')
                ->descriptionIcon('heroicon-o-pencil-square')
                ->color('info'),
        ];
    }
}

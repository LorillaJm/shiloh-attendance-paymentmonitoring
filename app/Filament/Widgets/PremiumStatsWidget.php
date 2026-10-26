<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Attendance;
use App\Models\Session;
use Illuminate\Support\Facades\DB;

class PremiumStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.premium-stats-widget';

    protected int | string | array $columnSpan = 'full';

    public function getStats(): array
    {
        return [
            [
                'label' => 'Total Students',
                'value' => Student::count(),
                'change' => $this->getStudentChange(),
                'changeType' => $this->getStudentChange() >= 0 ? 'positive' : 'negative',
                'icon' => 'heroicon-o-users',
                'iconColor' => 'text-blue-600',
                'iconBgColor' => 'bg-blue-50',
            ],
            [
                'label' => 'Total Revenue',
                'value' => '₱' . number_format(Payment::sum('amount'), 2),
                'change' => $this->getRevenueChange(),
                'changeType' => $this->getRevenueChange() >= 0 ? 'positive' : 'negative',
                'icon' => 'heroicon-o-currency-dollar',
                'iconColor' => 'text-green-600',
                'iconBgColor' => 'bg-green-50',
            ],
            [
                'label' => 'Attendance Rate',
                'value' => number_format($this->getAttendanceRate(), 1) . '%',
                'change' => $this->getAttendanceChange(),
                'changeType' => $this->getAttendanceChange() >= 0 ? 'positive' : 'negative',
                'icon' => 'heroicon-o-check-circle',
                'iconColor' => 'text-purple-600',
                'iconBgColor' => 'bg-purple-50',
            ],
            [
                'label' => 'Active Sessions',
                'value' => Session::where('status', 'active')->count(),
                'change' => null,
                'changeType' => 'neutral',
                'icon' => 'heroicon-o-academic-cap',
                'iconColor' => 'text-indigo-600',
                'iconBgColor' => 'bg-indigo-50',
            ],
        ];
    }

    protected function getStudentChange(): float
    {
        $currentMonth = Student::whereMonth('created_at', now()->month)->count();
        $lastMonth = Student::whereMonth('created_at', now()->subMonth()->month)->count();

        if ($lastMonth === 0) {
            return 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    protected function getRevenueChange(): float
    {
        $currentMonth = Payment::whereMonth('created_at', now()->month)->sum('amount');
        $lastMonth = Payment::whereMonth('created_at', now()->subMonth()->month)->sum('amount');

        if ($lastMonth == 0) {
            return 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    protected function getAttendanceRate(): float
    {
        $totalSessions = Attendance::count();
        
        if ($totalSessions === 0) {
            return 0;
        }

        $presentSessions = Attendance::where('status', 'present')->count();
        
        return ($presentSessions / $totalSessions) * 100;
    }

    protected function getAttendanceChange(): float
    {
        $currentMonthRate = $this->getMonthlyAttendanceRate(now()->month);
        $lastMonthRate = $this->getMonthlyAttendanceRate(now()->subMonth()->month);

        if ($lastMonthRate == 0) {
            return 0;
        }

        return round($currentMonthRate - $lastMonthRate, 1);
    }

    protected function getMonthlyAttendanceRate(int $month): float
    {
        $totalSessions = Attendance::whereMonth('created_at', $month)->count();
        
        if ($totalSessions === 0) {
            return 0;
        }

        $presentSessions = Attendance::whereMonth('created_at', $month)
            ->where('status', 'present')
            ->count();
        
        return ($presentSessions / $totalSessions) * 100;
    }
}

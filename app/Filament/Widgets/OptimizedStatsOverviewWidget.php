<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    // Reduced polling - only refresh every 5 minutes
    protected static ?string $pollingInterval = '300s';

    protected function getStats(): array
    {
        try {
            // Cache for 5 minutes - dashboard KPIs don't need real-time updates
            $stats = Cache::remember('dashboard_kpi_stats_v2', 300, function () {
                $today = now('Asia/Manila')->format('Y-m-d');
                $thisMonth = now('Asia/Manila');
                
                // Calculate next 15th
                $next15th = now('Asia/Manila');
                if ($next15th->day > 15) {
                    $next15th->addMonth();
                }
                $next15th->day(15);
                $next15thDate = $next15th->format('Y-m-d');

                // Single optimized query for all KPIs
                $kpis = DB::select("
                    SELECT 
                        (SELECT COUNT(*) FROM students WHERE status = 'ACTIVE') as total_students,
                        (SELECT COUNT(*) FROM enrollments WHERE status = 'ACTIVE' AND remaining_balance <= 0) as fully_paid,
                        (SELECT COUNT(*) FROM enrollments WHERE status = 'ACTIVE' AND remaining_balance > 0) as with_balance,
                        (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date = ?) as due_next_15th,
                        (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date < ?) as overdue,
                        (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules WHERE status = 'PAID' AND DATE(paid_at) = ?) as collected_today,
                        (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules WHERE status = 'PAID' AND YEAR(paid_at) = ? AND MONTH(paid_at) = ?) as collected_this_month
                ", [$next15thDate, $today, $today, $thisMonth->year, $thisMonth->month]);

                $data = $kpis[0];

                return [
                    'total_students' => $data->total_students,
                    'fully_paid' => $data->fully_paid,
                    'with_balance' => $data->with_balance,
                    'due_next_15th' => $data->due_next_15th,
                    'overdue' => $data->overdue,
                    'collected_today' => $data->collected_today,
                    'collected_this_month' => $data->collected_this_month,
                    'next_15th_date' => $next15th->format('M d, Y'),
                ];
            });

            return [
                Stat::make('Active Students', number_format($stats['total_students']))
                    ->description('Currently enrolled')
                    ->descriptionIcon('heroicon-o-user-group')
                    ->color('primary'),

                Stat::make('Fully Paid', number_format($stats['fully_paid']))
                    ->description('No balance remaining')
                    ->descriptionIcon('heroicon-o-check-circle')
                    ->color('success'),

                Stat::make('With Balance', number_format($stats['with_balance']))
                    ->description('Has remaining payments')
                    ->descriptionIcon('heroicon-o-banknotes')
                    ->color('warning'),

                Stat::make('Due Next 15th', number_format($stats['due_next_15th']))
                    ->description($stats['next_15th_date'])
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('info'),

                Stat::make('Overdue', number_format($stats['overdue']))
                    ->description('Needs attention')
                    ->descriptionIcon('heroicon-o-exclamation-triangle')
                    ->color('danger'),

                Stat::make('Collected Today', '₱' . number_format($stats['collected_today'], 2))
                    ->description('Today\'s collections')
                    ->descriptionIcon('heroicon-o-currency-dollar')
                    ->color('success'),
            ];
            
        } catch (\Exception $e) {
            \Log::error('OptimizedStatsOverviewWidget error: ' . $e->getMessage());
            
            return [
                Stat::make('Error', 'Unable to load stats')
                    ->description('Please refresh')
                    ->color('danger'),
            ];
        }
    }
}

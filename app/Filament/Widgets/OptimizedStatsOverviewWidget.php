<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0; // Changed to 0 to ensure it's first
    
    // No auto-polling - user can manually refresh
    protected static ?string $pollingInterval = null;
    
    // Make responsive - full width
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        try {
            // Cache for 3 minutes
            $stats = Cache::remember('dashboard_kpi_stats_v3', 180, function () {
                try {
                    $today = now('Asia/Manila')->format('Y-m-d');
                    $thisMonth = now('Asia/Manila');
                    
                    // hasTable() checks removed - each check costs ~200ms round-trip to Supabase
                    // Tables are guaranteed to exist after migration
                    
                    // Single optimized query - all KPIs in one shot
                    $kpis = DB::selectOne("
                        SELECT 
                            (SELECT COUNT(*) FROM students WHERE status = 'ACTIVE') as total_students,
                            (SELECT COUNT(*) FROM students WHERE status = 'ACTIVE' 
                             AND EXISTS (SELECT 1 FROM enrollments WHERE student_id = students.id AND status = 'ACTIVE')) as active_students,
                            (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date = ?) as due_today,
                            (SELECT COUNT(*) FROM payment_schedules WHERE status = 'UNPAID' AND due_date < ?) as overdue,
                            (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules WHERE status = 'PAID' AND DATE(paid_at) = ?) as collected_today,
                            (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules WHERE status = 'PAID' 
                             AND EXTRACT(YEAR FROM paid_at) = ? AND EXTRACT(MONTH FROM paid_at) = ?) as collected_this_month,
                            (SELECT COALESCE(SUM(remaining_balance), 0) FROM enrollments WHERE status = 'ACTIVE') as outstanding_balance
                    ", [$today, $today, $today, $thisMonth->year, $thisMonth->month]);

                    return [
                        'total_students' => $kpis->total_students ?? 0,
                        'active_students' => $kpis->active_students ?? 0,
                        'due_today' => $kpis->due_today ?? 0,
                        'overdue' => $kpis->overdue ?? 0,
                        'collected_today' => $kpis->collected_today ?? 0,
                        'collected_this_month' => $kpis->collected_this_month ?? 0,
                        'outstanding_balance' => $kpis->outstanding_balance ?? 0,
                    ];
                } catch (\Exception $e) {
                    \Log::error('OptimizedStatsOverviewWidget query error: ' . $e->getMessage());
                    return [
                        'total_students' => 0,
                        'active_students' => 0,
                        'due_today' => 0,
                        'overdue' => 0,
                        'collected_today' => 0,
                        'collected_this_month' => 0,
                        'outstanding_balance' => 0,
                    ];
                }
            });

            return [
                Stat::make('Total Students', number_format($stats['total_students']))
                    ->description('Registered in system')
                    ->color('primary')
                    ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Active Students', number_format($stats['active_students']))
                    ->description('With active enrollment')
                    ->color('success')
                    ->chart([3, 5, 6, 7, 8, 6, 7, 8])
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Due Today', number_format($stats['due_today']))
                    ->description('Scheduled for today')
                    ->color('warning')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Overdue', number_format($stats['overdue']))
                    ->description('Needs attention')
                    ->color('danger')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Today', '₱' . number_format($stats['collected_today'], 2))
                    ->description('Collections received')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform stat-card-currency',
                    ]),

                Stat::make('This Month', '₱' . number_format($stats['collected_this_month'], 2))
                    ->description('Month to date')
                    ->color('info')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform stat-card-currency',
                    ]),

                Stat::make('Outstanding', '₱' . number_format($stats['outstanding_balance'], 2))
                    ->description('Total receivables')
                    ->color('gray')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform stat-card-currency',
                    ]),
            ];
            
        } catch (\Exception $e) {
            \Log::error('OptimizedStatsOverviewWidget error: ' . $e->getMessage());
            \Log::error('OptimizedStatsOverviewWidget trace: ' . $e->getTraceAsString());
            
            // Return empty array instead of crashing
            return [];
        }
    }
}

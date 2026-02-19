<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        try {
            $stats = Cache::remember('dashboard_kpi_stats', 30, function () {
                // Get next 15th date
                $today = now('Asia/Manila');
                $next15th = $today->copy();
                if ($today->day <= 15) {
                    $next15th->day(15);
                } else {
                    $next15th->addMonth()->day(15);
                }

                // Total Active Students
                $totalStudents = Student::where('status', 'ACTIVE')->count();

                // Fully Paid Count (enrollments with remaining_balance = 0)
                $fullyPaid = Enrollment::where('status', 'ACTIVE')
                    ->where('remaining_balance', '<=', 0)
                    ->count();

                // With Balance Count (enrollments with remaining_balance > 0)
                $withBalance = Enrollment::where('status', 'ACTIVE')
                    ->where('remaining_balance', '>', 0)
                    ->count();

                // Due Next 15th Count
                $dueNext15th = PaymentSchedule::where('status', 'UNPAID')
                    ->whereDate('due_date', $next15th->format('Y-m-d'))
                    ->count();

                // Overdue Count
                $overdue = PaymentSchedule::where('status', 'UNPAID')
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', $today->format('Y-m-d'))
                    ->count();

                // Collected Today
                $collectedToday = PaymentSchedule::where('status', 'PAID')
                    ->whereDate('paid_at', $today->format('Y-m-d'))
                    ->sum('amount_due');

                // Collected This Month
                $collectedThisMonth = PaymentSchedule::where('status', 'PAID')
                    ->whereYear('paid_at', $today->year)
                    ->whereMonth('paid_at', $today->month)
                    ->sum('amount_due');

                return [
                    'total_students' => $totalStudents,
                    'fully_paid' => $fullyPaid,
                    'with_balance' => $withBalance,
                    'due_next_15th' => $dueNext15th,
                    'overdue' => $overdue,
                    'collected_today' => $collectedToday,
                    'collected_this_month' => $collectedThisMonth,
                    'next_15th_date' => $next15th->format('M d, Y'),
                ];
            });

            $statCards = [
                Stat::make('Active Students', number_format($stats['total_students']))
                    ->description('Currently enrolled')
                    ->descriptionIcon('heroicon-o-user-group')
                    ->color('primary')
                    ->chart([7, 8, 9, 10, 11, 12, $stats['total_students']]),

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

                Stat::make('Overdue Payments', number_format($stats['overdue']))
                    ->description('Past due date - needs attention')
                    ->descriptionIcon('heroicon-o-exclamation-triangle')
                    ->color('danger'),

                Stat::make('Collected Today', '₱' . number_format($stats['collected_today'], 2))
                    ->description('Today\'s collections')
                    ->descriptionIcon('heroicon-o-currency-dollar')
                    ->color('success'),
            ];

            // Add URLs only if routes exist
            try {
                if (route('filament.admin.resources.students.index', [], false)) {
                    $statCards[0] = $statCards[0]->url(route('filament.admin.resources.students.index', ['tableFilters' => ['status' => ['values' => ['ACTIVE']]]]));
                }
            } catch (\Exception $e) {
                // Route doesn't exist, skip URL
            }

            return $statCards;
            
        } catch (\Exception $e) {
            \Log::error('StatsOverviewWidget error: ' . $e->getMessage());
            
            // Return basic stats on error
            return [
                Stat::make('Error', 'Unable to load stats')
                    ->description('Please refresh the page')
                    ->descriptionIcon('heroicon-o-exclamation-triangle')
                    ->color('danger'),
            ];
        }
    }
}

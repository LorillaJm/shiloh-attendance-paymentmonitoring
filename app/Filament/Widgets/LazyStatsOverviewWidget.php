<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Services\DashboardCacheService;

class LazyStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    
    protected static ?string $pollingInterval = null;
    
    protected int | string | array $columnSpan = 'full';
    
    // Enable lazy loading
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        try {
            $stats = DashboardCacheService::getStudentCounts();
            $payments = DashboardCacheService::getPaymentsSummary(now());
            
            return [
                Stat::make('Total Students', number_format($stats['total']))
                    ->description('Registered in system')
                    ->color('primary')
                    ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Active Students', number_format($stats['active']))
                    ->description('With active enrollment')
                    ->color('success')
                    ->chart([3, 5, 6, 7, 8, 6, 7, 8])
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Due Today', number_format($payments['due_today'] ?? 0))
                    ->description('Scheduled for today')
                    ->color('warning')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Overdue', number_format($payments['overdue'] ?? 0))
                    ->description('Needs attention')
                    ->color('danger')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Today', '₱' . number_format($payments['collected_today'] ?? 0, 2))
                    ->description('Collections received')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform stat-card-currency',
                    ]),

                Stat::make('This Month', '₱' . number_format($payments['collected_this_month'] ?? 0, 2))
                    ->description('Month to date')
                    ->color('info')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform stat-card-currency',
                    ]),

                Stat::make('Outstanding', '₱' . number_format($payments['outstanding_balance'] ?? 0, 2))
                    ->description('Total receivables')
                    ->color('gray')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform stat-card-currency',
                    ]),
            ];
            
        } catch (\Exception $e) {
            \Log::error('LazyStatsOverviewWidget error: ' . $e->getMessage());
            return [];
        }
    }
}

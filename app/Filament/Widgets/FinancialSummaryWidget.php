<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FinancialSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 1; // Move to top with other KPIs
    
    protected static ?string $pollingInterval = null;
    
    // Full width to display with other stats
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        try {
            $stats = Cache::remember('dashboard_financial_summary_v1', 300, function () {
                $thisMonth = now('Asia/Manila');
                $lastMonth = now('Asia/Manila')->subMonth();
                
                $financial = DB::selectOne("
                    SELECT 
                        (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules 
                         WHERE status = 'PAID' 
                         AND EXTRACT(YEAR FROM paid_at) = ? 
                         AND EXTRACT(MONTH FROM paid_at) = ?) as revenue_this_month,
                        (SELECT COALESCE(SUM(amount_due), 0) FROM payment_schedules 
                         WHERE status = 'PAID' 
                         AND EXTRACT(YEAR FROM paid_at) = ? 
                         AND EXTRACT(MONTH FROM paid_at) = ?) as revenue_last_month,
                        (SELECT COALESCE(SUM(remaining_balance), 0) FROM enrollments 
                         WHERE status = 'ACTIVE') as outstanding_balance
                ", [
                    $thisMonth->year, $thisMonth->month,
                    $lastMonth->year, $lastMonth->month
                ]);

                $revenueThisMonth = $financial->revenue_this_month ?? 0;
                $revenueLastMonth = $financial->revenue_last_month ?? 0;
                $outstandingBalance = $financial->outstanding_balance ?? 0;
                
                // Calculate growth percentage
                $growthPercent = 0;
                if ($revenueLastMonth > 0) {
                    $growthPercent = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
                }

                return [
                    'revenue_this_month' => $revenueThisMonth,
                    'revenue_last_month' => $revenueLastMonth,
                    'growth_percent' => $growthPercent,
                    'outstanding_balance' => $outstandingBalance,
                ];
            });

            $growthColor = $stats['growth_percent'] >= 0 ? 'success' : 'danger';
            $growthIcon = $stats['growth_percent'] >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down';

            return [
                Stat::make('Revenue', '₱' . number_format($stats['revenue_this_month'], 2))
                    ->description(now('Asia/Manila')->format('F Y'))
                    ->descriptionIcon('heroicon-o-banknotes')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform stat-card-currency',
                    ]),

                Stat::make('Last Month', '₱' . number_format($stats['revenue_last_month'], 2))
                    ->description(now('Asia/Manila')->subMonth()->format('F Y'))
                    ->descriptionIcon('heroicon-o-chart-bar')
                    ->color('gray')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform stat-card-currency',
                    ]),

                Stat::make('Growth', number_format(abs($stats['growth_percent']), 1) . '%')
                    ->description($stats['growth_percent'] >= 0 ? 'Increase' : 'Decrease')
                    ->descriptionIcon($growthIcon)
                    ->color($growthColor)
                    ->extraAttributes([
                        'class' => 'stat-card-uniform',
                    ]),

                Stat::make('Outstanding', '₱' . number_format($stats['outstanding_balance'], 2))
                    ->description('Total receivables')
                    ->descriptionIcon('heroicon-o-currency-dollar')
                    ->color('warning')
                    ->extraAttributes([
                        'class' => 'stat-card-uniform stat-card-currency',
                    ]),
            ];
            
        } catch (\Exception $e) {
            \Log::error('FinancialSummaryWidget error: ' . $e->getMessage());
            return [];
        }
    }
}

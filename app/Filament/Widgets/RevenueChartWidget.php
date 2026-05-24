<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue Overview';
    
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Use payment_transactions for actual revenue (not payment_schedules)
        // Show last 30 days for a meaningful overview
        $days = 29;
        $startDate = now()->subDays($days)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        $revenues = Cache::remember('revenue_chart_30d', 300, function () use ($startDate, $endDate) {
            return DB::table('payment_transactions')
                ->select(DB::raw("DATE(transaction_date) as pay_date"), DB::raw("COALESCE(SUM(amount), 0) as total"))
                ->where('type', 'PAYMENT')
                ->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(transaction_date)'))
                ->pluck('total', 'pay_date')
                ->toArray();
        });

        $data = collect(range($days, 0))->map(function ($daysAgo) use ($revenues) {
            $date = now()->subDays($daysAgo);
            $key = $date->format('Y-m-d');
            return [
                'date' => $date->format('M d'),
                'revenue' => (float) ($revenues[$key] ?? 0),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Daily Revenue',
                    'data' => $data->pluck('revenue')->toArray(),
                    'backgroundColor' => 'rgba(0, 113, 227, 0.1)',
                    'borderColor' => 'rgb(0, 113, 227)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "₱" + value.toLocaleString(); }',
                    ],
                ],
            ],
        ];
    }
}

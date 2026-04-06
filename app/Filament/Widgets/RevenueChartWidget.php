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
        // Single query for all 7 days instead of 7 separate queries
        // Saves ~1.2 seconds (6 × 200ms round-trips to Supabase)
        $startDate = now()->subDays(6)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        $revenues = Cache::remember('revenue_chart_7d', 300, function () use ($startDate, $endDate) {
            return DB::table('payment_schedules')
                ->select(DB::raw("DATE(paid_at) as pay_date"), DB::raw("COALESCE(SUM(amount_due), 0) as total"))
                ->where('status', 'PAID')
                ->whereBetween(DB::raw('DATE(paid_at)'), [$startDate, $endDate])
                ->groupBy(DB::raw('DATE(paid_at)'))
                ->pluck('total', 'pay_date')
                ->toArray();
        });

        $data = collect(range(6, 0))->map(function ($daysAgo) use ($revenues) {
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

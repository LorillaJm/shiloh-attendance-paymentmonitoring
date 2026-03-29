<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue Overview';
    
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Get last 7 days of revenue data
        $data = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo)->format('Y-m-d');
            
            $revenue = DB::table('payment_schedules')
                ->where('status', 'PAID')
                ->whereDate('paid_at', $date)
                ->sum('amount_due');
            
            return [
                'date' => now()->subDays($daysAgo)->format('M d'),
                'revenue' => $revenue,
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

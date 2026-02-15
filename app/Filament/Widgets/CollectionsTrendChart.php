<?php

namespace App\Filament\Widgets;

use App\Models\PaymentSchedule;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CollectionsTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Collections Trend (Last 30 Days)';
    
    protected static ?int $sort = 2;
    
    protected static ?string $pollingInterval = '30s';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        return Cache::remember('dashboard_collections_trend', 60, function () {
            $today = now('Asia/Manila');
            $startDate = $today->copy()->subDays(29);

            // Get daily collections for last 30 days
            $collections = PaymentSchedule::where('status', 'PAID')
                ->whereBetween('paid_at', [$startDate->startOfDay(), $today->endOfDay()])
                ->select(
                    DB::raw('DATE(paid_at) as date'),
                    DB::raw('SUM(amount_due) as total')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            // Fill in missing dates with 0
            $labels = [];
            $data = [];
            
            for ($i = 29; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $labels[] = $date->format('M d');
                $data[] = $collections->get($dateStr)?->total ?? 0;
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Collections (₱)',
                        'data' => $data,
                        'borderColor' => '#10b981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
                'labels' => $labels,
            ];
        });
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
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) { return '₱' + value.toLocaleString(); }",
                    ],
                ],
            ],
        ];
    }
}

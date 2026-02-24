<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedCollectionsTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Collections Trend (Last 30 Days)';
    
    protected static ?int $sort = 10; // Move after all stat cards
    
    // No polling
    protected static ?string $pollingInterval = null;
    
    // Make chart larger
    protected static ?string $maxHeight = '400px';
    
    // Take full width
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Cache for 5 minutes
        return Cache::remember('dashboard_collections_trend_v3', 300, function () {
            $today = now('Asia/Manila');
            $startDate = $today->copy()->subDays(29); // Last 30 days

            // Optimized query - grouped by date
            $collections = DB::table('payment_schedules')
                ->select(
                    DB::raw('DATE(paid_at) as date'),
                    DB::raw('SUM(amount_due) as total')
                )
                ->where('status', 'PAID')
                ->whereBetween('paid_at', [$startDate->startOfDay(), $today->endOfDay()])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $labels = [];
            $data = [];
            
            // Build 30-day dataset
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
                        'tension' => 0.3,
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
                    'position' => 'top',
                ],
                'title' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "₱" + value.toLocaleString(); }',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }
}

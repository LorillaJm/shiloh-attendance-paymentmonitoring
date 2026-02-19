<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizedCollectionsTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Collections Trend (Last 7 Days)';
    
    protected static ?int $sort = 2;
    
    // No polling - user can manually refresh
    protected static ?string $pollingInterval = null;
    
    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        // Cache for 5 minutes
        return Cache::remember('dashboard_collections_trend_v2', 300, function () {
            $today = now('Asia/Manila');
            $startDate = $today->copy()->subDays(6); // Last 7 days

            // Optimized query - only last 7 days
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
            
            for ($i = 6; $i >= 0; $i--) {
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
}

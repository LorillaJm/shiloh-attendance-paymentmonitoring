<?php

namespace App\Filament\Widgets;

use App\Models\PaymentSchedule;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PaymentsDueTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Upcoming Payments (Next 3 Months - 15th)';
    
    protected static ?int $sort = 3;
    
    protected static ?string $pollingInterval = '30s';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        return Cache::remember('dashboard_payments_due_trend', 60, function () {
            $today = now('Asia/Manila');
            $labels = [];
            $data = [];

            // Get next 3 months' 15th dates
            for ($i = 0; $i < 3; $i++) {
                $date = $today->copy()->addMonths($i);
                
                // Determine the 15th for this month
                if ($i === 0 && $today->day > 15) {
                    // If today is past 15th, skip to next month
                    continue;
                }
                
                $fifteenth = $date->copy()->day(15);
                $labels[] = $fifteenth->format('M Y');

                // Count unpaid schedules due on this 15th
                $count = PaymentSchedule::where('status', 'UNPAID')
                    ->whereDate('due_date', $fifteenth->format('Y-m-d'))
                    ->count();

                $data[] = $count;
            }

            // If we skipped current month, add one more month
            if (count($labels) < 3) {
                $date = $today->copy()->addMonths(3);
                $fifteenth = $date->copy()->day(15);
                $labels[] = $fifteenth->format('M Y');

                $count = PaymentSchedule::where('status', 'UNPAID')
                    ->whereDate('due_date', $fifteenth->format('Y-m-d'))
                    ->count();

                $data[] = $count;
            }

            return [
                'datasets' => [
                    [
                        'label' => 'Payments Due',
                        'data' => $data,
                        'backgroundColor' => [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(59, 130, 246, 0.6)',
                            'rgba(59, 130, 246, 0.4)',
                        ],
                        'borderColor' => '#3b82f6',
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}

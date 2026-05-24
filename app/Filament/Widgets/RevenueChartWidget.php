<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue Overview';
    
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '300px';

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        $filters = [];
        $currentYear = (int) now('Asia/Manila')->format('Y');
        $currentMonth = (int) now('Asia/Manila')->format('m');

        // Current year: January up to current month
        $filters["year_{$currentYear}"] = "── {$currentYear} ──";
        for ($m = 1; $m <= $currentMonth; $m++) {
            $date = Carbon::create($currentYear, $m, 1);
            $filters[$date->format('Y-m')] = $date->format('F');
        }

        // Previous year: all 12 months
        $prevYear = $currentYear - 1;
        $filters["year_{$prevYear}"] = "── {$prevYear} ──";
        for ($m = 1; $m <= 12; $m++) {
            $date = Carbon::create($prevYear, $m, 1);
            $filters[$date->format('Y-m')] = $date->format('F');
        }

        return $filters;
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? now('Asia/Manila')->format('Y-m');
        
        // Handle year separator selection (e.g. "year_2026")
        if (str_starts_with($filter, 'year_')) {
            $filter = str_replace('year_', '', $filter) . '-01';
        }
        
        $selectedDate = Carbon::createFromFormat('Y-m', $filter)->timezone('Asia/Manila');
        $startDate = $selectedDate->copy()->startOfMonth();
        $endDate = $selectedDate->copy()->endOfMonth();
        $daysInMonth = $selectedDate->daysInMonth;

        $cacheKey = "revenue_chart_{$filter}";

        $revenues = Cache::remember($cacheKey, 300, function () use ($startDate, $endDate) {
            return DB::table('payment_transactions')
                ->select(DB::raw("DATE(transaction_date) as pay_date"), DB::raw("COALESCE(SUM(amount), 0) as total"))
                ->where('type', 'PAYMENT')
                ->whereBetween(DB::raw('DATE(transaction_date)'), [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->groupBy(DB::raw('DATE(transaction_date)'))
                ->pluck('total', 'pay_date')
                ->toArray();
        });

        $monthTotal = array_sum($revenues);

        $data = collect(range(1, $daysInMonth))->map(function ($day) use ($selectedDate, $revenues) {
            $date = $selectedDate->copy()->day($day);
            $key = $date->format('Y-m-d');
            return [
                'date' => $date->format('M d'),
                'revenue' => (float) ($revenues[$key] ?? 0),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Daily Revenue (Total: ₱' . number_format($monthTotal, 2) . ')',
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

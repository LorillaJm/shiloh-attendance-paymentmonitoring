<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Services\DashboardCacheService;

class ModernStatsWidget extends Widget
{
    protected static ?int $sort = 0;
    
    protected static string $view = 'filament.widgets.modern-stats-overview';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static bool $isLazy = true;

    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
        ];
    }

    protected function getStats(): array
    {
        try {
            $stats = DashboardCacheService::getStudentCounts();
            $payments = DashboardCacheService::getPaymentsSummary(now());
            
            return [
                [
                    'label' => 'Total Students',
                    'value' => number_format($stats['total']),
                    'description' => 'Registered in system',
                    'accent' => 'blue',
                    'icon' => 'users',
                    'chart' => [7, 3, 4, 5, 6, 3, 5, 3],
                ],
                [
                    'label' => 'Active Students',
                    'value' => number_format($stats['active']),
                    'description' => 'With active enrollment',
                    'accent' => 'emerald',
                    'icon' => 'user-check',
                    'chart' => [3, 5, 6, 7, 8, 6, 7, 8],
                ],
                [
                    'label' => 'Due Today',
                    'value' => number_format($payments['due_today'] ?? 0),
                    'description' => 'Scheduled for today',
                    'accent' => 'amber',
                    'icon' => 'clock',
                    'chart' => [2, 4, 3, 5, 4, 6, 5, 7],
                ],
                [
                    'label' => 'Overdue',
                    'value' => number_format($payments['overdue'] ?? 0),
                    'description' => 'Needs attention',
                    'accent' => 'rose',
                    'icon' => 'alert',
                    'chart' => [1, 2, 3, 2, 4, 3, 5, 4],
                ],
                [
                    'label' => 'Today',
                    'value' => '₱' . number_format($payments['collected_today'] ?? 0, 2),
                    'description' => 'Collections received',
                    'accent' => 'green',
                    'icon' => 'banknotes',
                    'currency' => true,
                    'chart' => [5, 8, 6, 9, 7, 10, 8, 12],
                ],
                [
                    'label' => 'This Month',
                    'value' => '₱' . number_format($payments['collected_this_month'] ?? 0, 2),
                    'description' => 'Month to date',
                    'accent' => 'cyan',
                    'icon' => 'calendar',
                    'currency' => true,
                    'chart' => [10, 15, 12, 18, 14, 20, 16, 22],
                ],
                [
                    'label' => 'Outstanding',
                    'value' => '₱' . number_format($payments['outstanding_balance'] ?? 0, 2),
                    'description' => 'Total receivables',
                    'accent' => 'slate',
                    'icon' => 'wallet',
                    'currency' => true,
                    'chart' => [20, 18, 19, 17, 18, 16, 17, 15],
                ],
            ];
            
        } catch (\Exception $e) {
            \Log::error('ModernStatsWidget error: ' . $e->getMessage());
            return [];
        }
    }
}

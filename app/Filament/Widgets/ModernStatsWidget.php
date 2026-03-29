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
            
            $colorMap = [
                'primary' => 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
                'success' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                'warning' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                'danger' => 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                'info' => 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
                'gray' => 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)',
            ];
            
            return [
                [
                    'label' => 'Total Students',
                    'value' => number_format($stats['total']),
                    'description' => 'Registered in system',
                    'color' => $colorMap['primary'],
                    'chart' => [7, 3, 4, 5, 6, 3, 5, 3],
                ],
                [
                    'label' => 'Active Students',
                    'value' => number_format($stats['active']),
                    'description' => 'With active enrollment',
                    'color' => $colorMap['success'],
                    'chart' => [3, 5, 6, 7, 8, 6, 7, 8],
                ],
                [
                    'label' => 'Due Today',
                    'value' => number_format($payments['due_today'] ?? 0),
                    'description' => 'Scheduled for today',
                    'color' => $colorMap['warning'],
                    'chart' => [2, 4, 3, 5, 4, 6, 5, 7],
                ],
                [
                    'label' => 'Overdue',
                    'value' => number_format($payments['overdue'] ?? 0),
                    'description' => 'Needs attention',
                    'color' => $colorMap['danger'],
                    'chart' => [1, 2, 3, 2, 4, 3, 5, 4],
                ],
                [
                    'label' => 'Today',
                    'value' => '₱' . number_format($payments['collected_today'] ?? 0, 2),
                    'description' => 'Collections received',
                    'color' => $colorMap['success'],
                    'chart' => [5, 8, 6, 9, 7, 10, 8, 12],
                ],
                [
                    'label' => 'This Month',
                    'value' => '₱' . number_format($payments['collected_this_month'] ?? 0, 2),
                    'description' => 'Month to date',
                    'color' => $colorMap['info'],
                    'chart' => [10, 15, 12, 18, 14, 20, 16, 22],
                ],
                [
                    'label' => 'Outstanding',
                    'value' => '₱' . number_format($payments['outstanding_balance'] ?? 0, 2),
                    'description' => 'Total receivables',
                    'color' => $colorMap['gray'],
                    'chart' => [20, 18, 19, 17, 18, 16, 17, 15],
                ],
            ];
            
        } catch (\Exception $e) {
            \Log::error('ModernStatsWidget error: ' . $e->getMessage());
            return [];
        }
    }
}

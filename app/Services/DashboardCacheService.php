<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DashboardCacheService
{
    /**
     * All dashboard cache keys
     */
    private static array $cacheKeys = [
        'dashboard_kpi_stats_v3',
        'dashboard_collections_trend_v3',
        'dashboard_alerts_v3',
        'dashboard_recent_payments_v3',
        'dashboard_financial_summary_v1',
        'dashboard_attendance_summary_v1',
    ];

    /**
     * Clear all dashboard caches
     */
    public static function clearAll(): void
    {
        foreach (self::$cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear payment-related caches (call after payment recorded)
     */
    public static function clearPaymentCaches(): void
    {
        Cache::forget('dashboard_kpi_stats_v3');
        Cache::forget('dashboard_collections_trend_v3');
        Cache::forget('dashboard_alerts_v3');
        Cache::forget('dashboard_recent_payments_v3');
        Cache::forget('dashboard_financial_summary_v1');
    }

    /**
     * Clear attendance-related caches (call after attendance recorded)
     */
    public static function clearAttendanceCaches(): void
    {
        Cache::forget('dashboard_attendance_summary_v1');
        Cache::forget('dashboard_alerts_v3');
    }

    /**
     * Clear student-related caches (call after student status change)
     */
    public static function clearStudentCaches(): void
    {
        Cache::forget('dashboard_kpi_stats_v3');
        Cache::forget('dashboard_alerts_v3');
    }

    /**
     * Warm up all dashboard caches (run via scheduled task)
     * Call this during off-peak hours to pre-populate caches
     */
    public static function warmUp(): void
    {
        try {
            app(\App\Filament\Widgets\OptimizedStatsOverviewWidget::class)->getStats();
            app(\App\Filament\Widgets\OptimizedCollectionsTrendChart::class)->getData();
            app(\App\Filament\Widgets\OptimizedAlertsWidget::class)->getAlerts();
            app(\App\Filament\Widgets\OptimizedRecentActivityWidget::class)->getRecentPayments();
            app(\App\Filament\Widgets\FinancialSummaryWidget::class)->getStats();
            app(\App\Filament\Widgets\AttendanceSummaryWidget::class)->getStats();
        } catch (\Exception $e) {
            \Log::error('Dashboard cache warmup failed: ' . $e->getMessage());
        }
    }
}

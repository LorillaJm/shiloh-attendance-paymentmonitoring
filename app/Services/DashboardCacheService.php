<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DashboardCacheService
{
    /**
     * Clear all dashboard caches
     */
    public static function clearAll(): void
    {
        $keys = [
            'dashboard_kpi_stats_v2',
            'dashboard_collections_trend_v2',
            'dashboard_alerts_v2',
            'dashboard_recent_payments_v2',
            'dashboard_attendance_snapshot',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear payment-related caches (call after payment recorded)
     */
    public static function clearPaymentCaches(): void
    {
        Cache::forget('dashboard_kpi_stats_v2');
        Cache::forget('dashboard_collections_trend_v2');
        Cache::forget('dashboard_alerts_v2');
        Cache::forget('dashboard_recent_payments_v2');
    }

    /**
     * Clear attendance-related caches (call after attendance recorded)
     */
    public static function clearAttendanceCaches(): void
    {
        Cache::forget('dashboard_attendance_snapshot');
        Cache::forget('dashboard_alerts_v2');
    }

    /**
     * Clear student-related caches (call after student status change)
     */
    public static function clearStudentCaches(): void
    {
        Cache::forget('dashboard_kpi_stats_v2');
        Cache::forget('dashboard_alerts_v2');
    }

    /**
     * Warm up all dashboard caches (run via scheduled task)
     */
    public static function warmUp(): void
    {
        // This will be called by a scheduled task to pre-populate caches
        // before peak usage times
        
        app(\App\Filament\Widgets\OptimizedStatsOverviewWidget::class)->getStats();
        app(\App\Filament\Widgets\OptimizedCollectionsTrendChart::class)->getData();
        app(\App\Filament\Widgets\OptimizedAlertsWidget::class)->getAlerts();
        app(\App\Filament\Widgets\OptimizedRecentActivityWidget::class)->getRecentPayments();
    }
}

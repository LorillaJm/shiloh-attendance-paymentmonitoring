<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class StudentCacheService
{
    /**
     * Clear student status counts cache
     * Call this after creating, updating, or deleting students
     */
    public static function clearStatusCounts(): void
    {
        Cache::forget('student_status_counts');
    }

    /**
     * Clear all student-related caches
     */
    public static function clearAll(): void
    {
        Cache::forget('student_status_counts');
        
        // Also clear dashboard caches that depend on student data
        Cache::forget('dashboard_kpi_stats_v3');
        Cache::forget('dashboard_alerts_v3');
    }
}

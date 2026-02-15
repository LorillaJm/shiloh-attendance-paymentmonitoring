<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configure caching for dashboard widgets to improve performance.
    | All times are in seconds.
    |
    */

    'cache' => [
        // KPI stats cache duration (30 seconds)
        'kpi_stats_ttl' => env('DASHBOARD_KPI_CACHE_TTL', 30),
        
        // Collections trend cache duration (60 seconds)
        'collections_trend_ttl' => env('DASHBOARD_COLLECTIONS_TREND_TTL', 60),
        
        // Payments due trend cache duration (60 seconds)
        'payments_due_trend_ttl' => env('DASHBOARD_PAYMENTS_DUE_TREND_TTL', 60),
        
        // Attendance snapshot cache duration (60 seconds)
        'attendance_snapshot_ttl' => env('DASHBOARD_ATTENDANCE_SNAPSHOT_TTL', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Polling Intervals
    |--------------------------------------------------------------------------
    |
    | Configure how often widgets refresh their data.
    | Set to null to disable polling for a widget.
    | All times are in seconds.
    |
    */

    'polling' => [
        // Stats overview widget (15 seconds)
        'stats_overview' => env('DASHBOARD_STATS_POLLING', 15),
        
        // Chart widgets (30 seconds)
        'charts' => env('DASHBOARD_CHARTS_POLLING', 30),
        
        // Table widgets (20 seconds for due/overdue, 15 for recent)
        'due_next_table' => env('DASHBOARD_DUE_NEXT_POLLING', 20),
        'overdue_table' => env('DASHBOARD_OVERDUE_POLLING', 20),
        'recent_payments_table' => env('DASHBOARD_RECENT_PAYMENTS_POLLING', 15),
        
        // Attendance snapshot (30 seconds)
        'attendance_snapshot' => env('DASHBOARD_ATTENDANCE_POLLING', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Dashboard Table Limits
    |--------------------------------------------------------------------------
    |
    | Configure how many rows to display in dashboard tables.
    |
    */

    'table_limits' => [
        'due_next' => 20,
        'overdue' => 20,
        'recent_payments' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Timezone
    |--------------------------------------------------------------------------
    |
    | The timezone used for all dashboard date calculations.
    |
    */

    'timezone' => env('DASHBOARD_TIMEZONE', 'Asia/Manila'),

    /*
    |--------------------------------------------------------------------------
    | Collections Trend Period
    |--------------------------------------------------------------------------
    |
    | Number of days to show in the collections trend chart.
    |
    */

    'collections_trend_days' => 30,

    /*
    |--------------------------------------------------------------------------
    | Payments Due Forecast
    |--------------------------------------------------------------------------
    |
    | Number of months to forecast for payments due trend.
    |
    */

    'payments_due_months' => 3,

    /*
    |--------------------------------------------------------------------------
    | Attendance Snapshot Period
    |--------------------------------------------------------------------------
    |
    | Number of days to include in attendance snapshot statistics.
    |
    */

    'attendance_snapshot_days' => 7,
];

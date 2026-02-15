<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Real-time Polling Configuration
    |--------------------------------------------------------------------------
    |
    | Configure polling intervals for near real-time updates across the system.
    | Intervals are in seconds. Set to null to disable polling.
    |
    */

    // Dashboard KPI widgets polling interval
    'dashboard_poll_interval' => env('DASHBOARD_POLL_INTERVAL', 15),

    // Due payments list polling interval
    'due_payments_poll_interval' => env('DUE_PAYMENTS_POLL_INTERVAL', 20),

    // Overdue payments list polling interval
    'overdue_payments_poll_interval' => env('OVERDUE_PAYMENTS_POLL_INTERVAL', 20),

    // Reports polling interval
    'reports_poll_interval' => env('REPORTS_POLL_INTERVAL', 30),

    // Cache TTL for polled data (seconds)
    'cache_ttl' => [
        'dashboard_stats' => 10,  // Dashboard stats cached for 10 seconds
        'due_count' => 15,        // Due count cached for 15 seconds
        'overdue_count' => 15,    // Overdue count cached for 15 seconds
        'collections' => 30,      // Collection stats cached for 30 seconds
    ],

    // Enable/disable polling globally
    'enabled' => env('REALTIME_POLLING_ENABLED', true),

    // Role-based polling (which roles can see real-time updates)
    'allowed_roles' => ['ADMIN', 'USER'],
];

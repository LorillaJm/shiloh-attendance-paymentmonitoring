<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Database health check endpoint (for monitoring)
Route::get('/health/database', function () {
    try {
        $start = microtime(true);
        \DB::connection()->getPdo();
        $connectionTime = round((microtime(true) - $start) * 1000, 2);

        $start = microtime(true);
        $studentCount = \DB::table('students')->count();
        $queryTime = round((microtime(true) - $start) * 1000, 2);

        return response()->json([
            'status' => 'healthy',
            'database' => [
                'connected' => true,
                'connection_time_ms' => $connectionTime,
                'query_time_ms' => $queryTime,
                'host' => config('database.connections.pgsql.host'),
                'port' => config('database.connections.pgsql.port'),
                'database' => config('database.connections.pgsql.database'),
            ],
            'metrics' => [
                'students_count' => $studentCount,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'database' => [
                'connected' => false,
                'error' => $e->getMessage(),
            ],
            'timestamp' => now()->toIso8601String(),
        ], 503);
    }
})->name('health.database');

// Database health check endpoint (for monitoring)
Route::get('/health/database', function () {
    try {
        $start = microtime(true);
        \DB::connection()->getPdo();
        $connectionTime = round((microtime(true) - $start) * 1000, 2);

        $start = microtime(true);
        $studentCount = \DB::table('students')->count();
        $queryTime = round((microtime(true) - $start) * 1000, 2);

        return response()->json([
            'status' => 'healthy',
            'database' => [
                'connected' => true,
                'connection_time_ms' => $connectionTime,
                'query_time_ms' => $queryTime,
                'host' => config('database.connections.pgsql.host'),
                'port' => config('database.connections.pgsql.port'),
                'database' => config('database.connections.pgsql.database'),
            ],
            'metrics' => [
                'students_count' => $studentCount,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'database' => [
                'connected' => false,
                'error' => $e->getMessage(),
            ],
            'timestamp' => now()->toIso8601String(),
        ], 503);
    }
})->name('health.database');

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/diagnostic/db-config', function () {
    return response()->json([
        'host' => config('database.connections.pgsql.host'),
        'port' => config('database.connections.pgsql.port'),
        'database' => config('database.connections.pgsql.database'),
        'username' => config('database.connections.pgsql.username'),
        'sslmode' => config('database.connections.pgsql.sslmode'),
        'timeout' => config('database.connections.pgsql.options')[PDO::ATTR_TIMEOUT] ?? 'not set',
    ]);
});

Route::get('/diagnostic/db-test', function () {
    try {
        DB::select('SELECT 1');
        
        // Check if migrations table exists
        $hasMigrations = Schema::hasTable('migrations');
        
        // Check if users table exists
        $hasUsers = Schema::hasTable('users');
        
        // Get migration count
        $migrationCount = $hasMigrations ? DB::table('migrations')->count() : 0;
        
        return response()->json([
            'success' => true,
            'message' => 'Database connection working',
            'tables' => [
                'migrations' => $hasMigrations,
                'users' => $hasUsers,
            ],
            'migration_count' => $migrationCount,
            'needs_migration' => !$hasUsers,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

Route::get('/diagnostic/network-test', function () {
    $host = config('database.connections.pgsql.host');
    $port = config('database.connections.pgsql.port');
    
    // Test if we can reach the host
    $connection = @fsockopen($host, $port, $errno, $errstr, 5);
    
    if ($connection) {
        fclose($connection);
        return response()->json([
            'success' => true,
            'message' => "Can reach {$host}:{$port}",
            'host' => $host,
            'port' => $port,
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => "Cannot reach {$host}:{$port}",
            'error' => $errstr,
            'errno' => $errno,
            'host' => $host,
            'port' => $port,
        ]);
    }
});

Route::get('/diagnostic/tables', function () {
    try {
        $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
        
        return response()->json([
            'success' => true,
            'count' => count($tables),
            'tables' => array_map(fn($t) => $t->tablename, $tables),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
});

<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

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

Route::get('/diagnostic/supabase-alternatives', function () {
    // Get Supabase project reference from the host
    $host = config('database.connections.pgsql.host');
    $projectRef = explode('.', $host)[0] ?? 'unknown';
    
    return response()->json([
        'current_host' => $host,
        'current_port' => config('database.connections.pgsql.port'),
        'project_ref' => $projectRef,
        'alternatives' => [
            'direct_connection' => [
                'host' => "db.{$projectRef}.supabase.co",
                'port' => 5432,
                'note' => 'Direct connection (bypasses pooler)',
            ],
            'ipv4_pooler' => [
                'host' => $host,
                'port' => 6543,
                'note' => 'Transaction pooler (IPv4)',
            ],
            'session_pooler' => [
                'host' => $host,
                'port' => 5432,
                'note' => 'Session pooler',
            ],
        ],
        'recommendation' => 'Try using db.{project-ref}.supabase.co:5432 for direct connection',
    ]);
});

<?php
// Bootstrap Laravel to see the actual error
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    
    echo "Laravel loaded successfully!\n";
    echo "Environment: " . app()->environment() . "\n";
    echo "APP_KEY set: " . (config('app.key') ? 'Yes' : 'No') . "\n";
    echo "Database: " . config('database.default') . "\n";
    
    // Test database
    echo "\nTesting database connection...\n";
    $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public' LIMIT 5");
    echo "Tables found: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "  - " . $table->tablename . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

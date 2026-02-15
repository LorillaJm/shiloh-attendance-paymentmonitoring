<?php
// Run migrations manually
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    echo "Running migrations...\n\n";
    
    $status = $kernel->call('migrate', [
        '--force' => true,
    ]);
    
    echo "\n\nMigrations completed with status: $status\n";
    
    echo "\nOptimizing application...\n";
    $kernel->call('config:cache');
    $kernel->call('route:cache');
    $kernel->call('view:cache');
    
    echo "\nDone! Your app should work now.\n";
    echo "Visit: https://shiloh-attendance-paymentmonitoring.onrender.com\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

<?php
// Clear all Laravel caches
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    echo "Clearing caches...\n\n";
    
    // Clear config cache
    $kernel->call('config:clear');
    echo "✓ Config cache cleared\n";
    
    // Clear route cache
    $kernel->call('route:clear');
    echo "✓ Route cache cleared\n";
    
    // Clear view cache
    $kernel->call('view:clear');
    echo "✓ View cache cleared\n";
    
    // Clear cache
    $kernel->call('cache:clear');
    echo "✓ Application cache cleared\n";
    
    // Optimize
    echo "\nOptimizing...\n";
    $kernel->call('config:cache');
    echo "✓ Config cached\n";
    
    $kernel->call('route:cache');
    echo "✓ Routes cached\n";
    
    $kernel->call('view:cache');
    echo "✓ Views cached\n";
    
    echo "\n✅ All caches cleared and optimized!\n";
    echo "\nNow visit: https://shiloh-attendance-paymentmonitoring.onrender.com\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}

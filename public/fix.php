<?php
// Manually clear view cache and test
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    echo "Clearing view cache manually...\n";
    
    // Delete compiled views
    $viewPath = __DIR__ . '/../storage/framework/views';
    if (is_dir($viewPath)) {
        $files = glob($viewPath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✓ Deleted " . count($files) . " compiled views\n";
    }
    
    // Clear bootstrap cache
    $cachePath = __DIR__ . '/../bootstrap/cache';
    $cacheFiles = glob($cachePath . '/*.php');
    foreach ($cacheFiles as $file) {
        if (basename($file) !== '.gitignore') {
            unlink($file);
            echo "✓ Deleted cache: " . basename($file) . "\n";
        }
    }
    
    echo "\n✅ All caches cleared!\n";
    echo "\nNow try: https://shiloh-attendance-paymentmonitoring.onrender.com\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

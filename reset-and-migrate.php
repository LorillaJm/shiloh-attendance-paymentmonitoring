<?php

// Reset database connection and run migrations
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Close any existing connections
DB::disconnect();

// Reconnect
DB::reconnect();

echo "Running migrations...\n";

// Run migrations
Artisan::call('migrate', ['--force' => true]);

echo Artisan::output();

echo "\nDone!\n";

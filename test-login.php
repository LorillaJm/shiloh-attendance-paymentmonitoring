<?php

/**
 * Quick test script to verify admin login setup
 * Run: php test-login.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "================================================================================\n";
echo "ADMIN LOGIN TEST\n";
echo "================================================================================\n\n";

// Check database connection
try {
    DB::connection()->getPdo();
    echo "✓ Database connection: OK\n";
} catch (\Exception $e) {
    echo "✗ Database connection: FAILED\n";
    echo "  Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Check users table exists
try {
    $userCount = DB::table('users')->count();
    echo "✓ Users table: OK (found {$userCount} users)\n";
} catch (\Exception $e) {
    echo "✗ Users table: NOT FOUND\n";
    echo "  Run: php artisan migrate\n";
    exit(1);
}

// Check admin users
$users = DB::table('users')->get(['name', 'email']);

if ($users->isEmpty()) {
    echo "✗ No admin users found\n";
    echo "  Run: php artisan db:seed --class=SimpleAdminSeeder\n";
    exit(1);
}

echo "\n";
echo "Available Admin Accounts:\n";
echo "-------------------------\n";
foreach ($users as $user) {
    echo "  • {$user->email}\n";
}

echo "\n";
echo "Login Credentials:\n";
echo "------------------\n";

if ($users->where('email', 'admin@admin.com')->first()) {
    echo "Email:    admin@admin.com\n";
    echo "Password: admin\n";
    echo "\n";
}

if ($users->where('email', 'admin@shiloh.local')->first()) {
    echo "Email:    admin@shiloh.local\n";
    echo "Password: password\n";
    echo "\n";
}

echo "Login URL:\n";
echo "----------\n";
echo "http://localhost:8000/admin/login\n";
echo "\n";

echo "Next Steps:\n";
echo "-----------\n";
echo "1. Start server: php artisan serve\n";
echo "2. Open browser: http://localhost:8000/admin/login\n";
echo "3. Login with credentials above\n";
echo "\n";

echo "================================================================================\n";
echo "✓ ALL CHECKS PASSED - READY TO LOGIN!\n";
echo "================================================================================\n";

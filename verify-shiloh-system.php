<?php

/**
 * Shiloh System Verification Script
 * Run: php verify-shiloh-system.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\SessionType;
use App\Models\StudentSchedule;
use App\Models\SessionOccurrence;
use App\Models\PaymentTransaction;
use App\Enums\UserRole;

echo "🔍 Verifying Shiloh System...\n\n";

// Check migrations
echo "📊 Database Tables:\n";
$tables = [
    'users', 'guardians', 'guardian_student', 'students', 
    'session_types', 'student_schedules', 'session_occurrences',
    'enrollments', 'payment_schedules', 'payment_transactions',
    'attendance_records'
];

foreach ($tables as $table) {
    $exists = \Schema::hasTable($table);
    echo ($exists ? "  ✓" : "  ✗") . " {$table}\n";
}

echo "\n👥 User Roles:\n";
$roles = ['SUPERADMIN', 'ADMIN', 'PARENT'];
foreach ($roles as $role) {
    $count = User::where('role', $role)->count();
    echo "  {$role}: {$count} users\n";
}

echo "\n📚 Session Types:\n";
$sessionTypes = SessionType::all();
foreach ($sessionTypes as $type) {
    echo "  ✓ {$type->name} ({$type->code})\n";
}

echo "\n👨‍👩‍👧 Students & Guardians:\n";
echo "  Students: " . Student::count() . "\n";
echo "  Guardians: " . Guardian::count() . "\n";
echo "  Students with guardians: " . Student::has('guardians')->count() . "\n";
echo "  Students requiring monitoring (age ≤ 10): " . Student::where('requires_monitoring', true)->count() . "\n";

echo "\n📅 Schedules & Sessions:\n";
echo "  Student Schedules: " . StudentSchedule::count() . "\n";
echo "  Session Occurrences: " . SessionOccurrence::count() . "\n";
echo "  Active Schedules: " . StudentSchedule::where('is_active', true)->count() . "\n";

echo "\n💰 Payments:\n";
echo "  Payment Transactions: " . PaymentTransaction::count() . "\n";
echo "  Total Payments: ₱" . number_format(PaymentTransaction::where('type', 'PAYMENT')->sum('amount'), 2) . "\n";

echo "\n✅ System Status:\n";
$allGood = true;

// Check critical tables
if (!Schema::hasTable('guardians')) {
    echo "  ✗ Guardians table missing\n";
    $allGood = false;
}

if (!Schema::hasTable('session_types')) {
    echo "  ✗ Session types table missing\n";
    $allGood = false;
}

if (SessionType::count() === 0) {
    echo "  ⚠️  No session types - run: php artisan db:seed --class=SessionTypeSeeder\n";
    $allGood = false;
}

if (User::where('role', 'ADMIN')->count() === 0) {
    echo "  ⚠️  No admin users - run: php artisan db:seed --class=SuperadminSeeder\n";
    $allGood = false;
}

if ($allGood) {
    echo "  ✅ All systems operational!\n";
} else {
    echo "  ⚠️  Some issues found - see above\n";
}

echo "\n📝 Next Steps:\n";
echo "  1. Create student schedules via admin panel\n";
echo "  2. Generate session occurrences: php artisan sessions:generate --days=30\n";
echo "  3. Setup cron for scheduler\n";
echo "  4. Test all user roles\n";
echo "  5. Configure email for reminders\n";

echo "\n🎉 Verification complete!\n";

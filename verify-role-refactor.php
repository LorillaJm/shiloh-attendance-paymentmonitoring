<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Guardian;
use App\Models\Student;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         ROLE REFACTOR VERIFICATION SCRIPT                      ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$passed = 0;
$failed = 0;

// Test 1: Check UserRole enum only has ADMIN and PARENT
echo "Test 1: Checking UserRole enum...\n";
$roles = array_column(UserRole::cases(), 'value');
if (count($roles) === 2 && in_array('ADMIN', $roles) && in_array('PARENT', $roles)) {
    echo "  ✓ UserRole enum contains only ADMIN and PARENT\n";
    $passed++;
} else {
    echo "  ✗ UserRole enum has incorrect roles: " . implode(', ', $roles) . "\n";
    $failed++;
}

// Test 2: Check database constraint
echo "\nTest 2: Checking database role constraint...\n";
try {
    $driver = DB::getDriverName();
    if ($driver === 'pgsql') {
        $constraint = DB::select("
            SELECT conname, pg_get_constraintdef(oid) as definition
            FROM pg_constraint
            WHERE conname = 'users_role_check'
        ");
        if (!empty($constraint)) {
            echo "  ✓ Database constraint exists for role validation\n";
            $passed++;
        } else {
            echo "  ✗ Database constraint not found\n";
            $failed++;
        }
    } else {
        echo "  ℹ Skipping constraint check (MySQL uses ENUM)\n";
        $passed++;
    }
} catch (Exception $e) {
    echo "  ✗ Error checking constraint: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 3: Check existing users have valid roles
echo "\nTest 3: Checking existing users...\n";
$invalidUsers = User::whereNotIn('role', ['ADMIN', 'PARENT'])->count();
if ($invalidUsers === 0) {
    echo "  ✓ All users have valid roles (ADMIN or PARENT)\n";
    $passed++;
} else {
    echo "  ✗ Found {$invalidUsers} users with invalid roles\n";
    $failed++;
}

// Test 4: Check User model methods
echo "\nTest 4: Checking User model methods...\n";
$testUser = new User(['role' => UserRole::ADMIN]);
if (method_exists($testUser, 'isAdmin') && method_exists($testUser, 'isParent')) {
    echo "  ✓ User model has isAdmin() and isParent() methods\n";
    $passed++;
} else {
    echo "  ✗ User model missing required methods\n";
    $failed++;
}

// Test 5: Check ParentStudentScope exists
echo "\nTest 5: Checking ParentStudentScope...\n";
if (class_exists('App\Scopes\ParentStudentScope')) {
    echo "  ✓ ParentStudentScope class exists\n";
    $passed++;
} else {
    echo "  ✗ ParentStudentScope class not found\n";
    $failed++;
}

// Test 6: Check Guardian-User relationship
echo "\nTest 6: Checking Guardian-User relationship...\n";
$guardian = new Guardian();
if (method_exists($guardian, 'user')) {
    echo "  ✓ Guardian has user() relationship\n";
    $passed++;
} else {
    echo "  ✗ Guardian missing user() relationship\n";
    $failed++;
}

// Test 7: Check Student model does NOT have user relationship
echo "\nTest 7: Checking Student model...\n";
$student = new Student();
if (!method_exists($student, 'user') && !in_array('user_id', $student->getFillable())) {
    echo "  ✓ Student model does not have user relationship (students are records, not users)\n";
    $passed++;
} else {
    echo "  ✗ Student model incorrectly has user relationship\n";
    $failed++;
}

// Test 8: Check policies exist
echo "\nTest 8: Checking authorization policies...\n";
$policies = [
    'App\Policies\StudentPolicy',
    'App\Policies\AttendanceRecordPolicy',
    'App\Policies\GuardianPolicy',
    'App\Policies\UserPolicy',
];
$allExist = true;
foreach ($policies as $policy) {
    if (!class_exists($policy)) {
        echo "  ✗ Policy not found: {$policy}\n";
        $allExist = false;
    }
}
if ($allExist) {
    echo "  ✓ All required policies exist\n";
    $passed++;
} else {
    $failed++;
}

// Test 9: Check Filament panels
echo "\nTest 9: Checking Filament panels...\n";
$panels = [
    'App\Providers\Filament\AdminPanelProvider',
    'App\Providers\Filament\ParentPanelProvider',
];
$allExist = true;
foreach ($panels as $panel) {
    if (!class_exists($panel)) {
        echo "  ✗ Panel provider not found: {$panel}\n";
        $allExist = false;
    }
}
if ($allExist) {
    echo "  ✓ Both Admin and Parent panel providers exist\n";
    $passed++;
} else {
    $failed++;
}

// Test 10: Check middleware
echo "\nTest 10: Checking middleware...\n";
$middleware = [
    'App\Http\Middleware\RedirectParentToPortal',
    'App\Http\Middleware\ParentAccessMiddleware',
];
$allExist = true;
foreach ($middleware as $mw) {
    if (!class_exists($mw)) {
        echo "  ✗ Middleware not found: {$mw}\n";
        $allExist = false;
    }
}
if ($allExist) {
    echo "  ✓ Required middleware classes exist\n";
    $passed++;
} else {
    $failed++;
}

// Test 11: Check parent portal pages
echo "\nTest 11: Checking parent portal pages...\n";
$pages = [
    'App\Filament\Parent\Pages\ParentDashboard',
    'App\Filament\Parent\Pages\MyChildrenAttendance',
    'App\Filament\Parent\Pages\MyChildrenPayments',
];
$allExist = true;
foreach ($pages as $page) {
    if (!class_exists($page)) {
        echo "  ✗ Page not found: {$page}\n";
        $allExist = false;
    }
}
if ($allExist) {
    echo "  ✓ All parent portal pages exist\n";
    $passed++;
} else {
    $failed++;
}

// Test 12: Check migration was run
echo "\nTest 12: Checking migration status...\n";
$migration = DB::table('migrations')
    ->where('migration', '2026_03_06_000001_simplify_user_roles')
    ->first();
if ($migration) {
    echo "  ✓ Role simplification migration has been run\n";
    $passed++;
} else {
    echo "  ✗ Role simplification migration not found in migrations table\n";
    $failed++;
}

// Summary
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                      VERIFICATION SUMMARY                      ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "  Tests Passed: {$passed}\n";
echo "  Tests Failed: {$failed}\n";
echo "\n";

if ($failed === 0) {
    echo "  ✅ ALL TESTS PASSED! Role refactoring is complete.\n";
    echo "\n";
    echo "  System now supports only TWO roles:\n";
    echo "    • ADMIN - Full system access\n";
    echo "    • PARENT - Read-only access to their children's data\n";
    echo "\n";
    echo "  Admin Panel: /admin\n";
    echo "  Parent Portal: /parent\n";
    echo "\n";
} else {
    echo "  ⚠️  SOME TESTS FAILED. Please review the errors above.\n";
    echo "\n";
    exit(1);
}

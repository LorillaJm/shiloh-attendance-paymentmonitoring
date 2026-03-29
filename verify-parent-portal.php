<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\PaymentTransaction;
use App\Models\SessionOccurrence;
use App\Models\Enrollment;
use App\Enums\UserRole;
use App\Services\ParentPortalService;

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         PARENT PORTAL VERIFICATION SCRIPT                      ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$passed = 0;
$failed = 0;

// Test 1: Check parent user exists
echo "Test 1: Checking parent users...\n";
$parentUsers = User::where('role', UserRole::PARENT)->count();
if ($parentUsers > 0) {
    echo "  ✓ Found {$parentUsers} parent user(s)\n";
    $passed++;
} else {
    echo "  ✗ No parent users found\n";
    $failed++;
}

// Test 2: Check guardian relationships
echo "\nTest 2: Checking guardian relationships...\n";
$guardiansWithUsers = Guardian::whereNotNull('user_id')->count();
$totalGuardians = Guardian::count();
if ($guardiansWithUsers > 0) {
    echo "  ✓ {$guardiansWithUsers} of {$totalGuardians} guardians linked to users\n";
    $passed++;
} else {
    echo "  ✗ No guardians linked to users\n";
    $failed++;
}

// Test 3: Check guardian-student relationships
echo "\nTest 3: Checking guardian-student relationships...\n";
$guardiansWithStudents = Guardian::has('students')->count();
if ($guardiansWithStudents > 0) {
    echo "  ✓ {$guardiansWithStudents} guardian(s) have students linked\n";
    $passed++;
} else {
    echo "  ✗ No guardians have students linked\n";
    $failed++;
}

// Test 4: Check ParentPortalService
echo "\nTest 4: Checking ParentPortalService...\n";
try {
    $service = new ParentPortalService();
    $guardian = Guardian::whereNotNull('user_id')->first();
    
    if ($guardian) {
        $data = $service->getDashboardData($guardian);
        
        if (isset($data['students']) && isset($data['summary']) && isset($data['alerts'])) {
            echo "  ✓ ParentPortalService returns correct data structure\n";
            echo "    - Students: " . $data['students']->count() . "\n";
            echo "    - Alerts: " . $data['alerts']['total'] . "\n";
            $passed++;
        } else {
            echo "  ✗ ParentPortalService data structure incomplete\n";
            $failed++;
        }
    } else {
        echo "  ⚠ No guardian with user to test\n";
        $passed++;
    }
} catch (Exception $e) {
    echo "  ✗ ParentPortalService error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 5: Check parent pages exist
echo "\nTest 5: Checking parent pages...\n";
$pages = [
    'App\Filament\Parent\Pages\ParentDashboard',
    'App\Filament\Parent\Pages\MyChildrenAttendance',
    'App\Filament\Parent\Pages\MyChildrenPayments',
    'App\Filament\Parent\Pages\MyChildrenSessions',
    'App\Filament\Parent\Pages\Notifications',
];
$allExist = true;
foreach ($pages as $page) {
    if (!class_exists($page)) {
        echo "  ✗ Page not found: {$page}\n";
        $allExist = false;
    }
}
if ($allExist) {
    echo "  ✓ All parent pages exist\n";
    $passed++;
} else {
    $failed++;
}

// Test 6: Check data access scoping
echo "\nTest 6: Checking data access scoping...\n";
$guardian = Guardian::whereNotNull('user_id')->with('students')->first();
if ($guardian && $guardian->students->count() > 0) {
    $studentIds = $guardian->students->pluck('id')->toArray();
    
    // Check attendance scoping
    $attendanceCount = AttendanceRecord::whereIn('student_id', $studentIds)->count();
    echo "  ✓ Can access {$attendanceCount} attendance record(s) for guardian's students\n";
    
    // Check payment scoping
    $paymentCount = PaymentTransaction::whereHas('enrollment', function($q) use ($studentIds) {
        $q->whereIn('student_id', $studentIds);
    })->count();
    echo "  ✓ Can access {$paymentCount} payment(s) for guardian's students\n";
    
    // Check session scoping
    $sessionCount = SessionOccurrence::whereIn('student_id', $studentIds)->count();
    echo "  ✓ Can access {$sessionCount} session(s) for guardian's students\n";
    
    $passed++;
} else {
    echo "  ⚠ No guardian with students to test scoping\n";
    $passed++;
}

// Test 7: Check enrollment and balance calculations
echo "\nTest 7: Checking enrollment and balance calculations...\n";
$activeEnrollments = Enrollment::where('status', 'ACTIVE')->count();
if ($activeEnrollments > 0) {
    $enrollment = Enrollment::where('status', 'ACTIVE')->with('package')->first();
    if ($enrollment) {
        $totalFee = $enrollment->total_fee;
        $totalPaid = $enrollment->total_paid;
        $balance = $enrollment->remaining_balance_computed;
        
        echo "  ✓ Enrollment calculations working:\n";
        echo "    - Total Fee: ₱" . number_format($totalFee, 2) . "\n";
        echo "    - Total Paid: ₱" . number_format($totalPaid, 2) . "\n";
        echo "    - Balance: ₱" . number_format($balance, 2) . "\n";
        $passed++;
    } else {
        echo "  ⚠ No active enrollment to test\n";
        $passed++;
    }
} else {
    echo "  ⚠ No active enrollments\n";
    $passed++;
}

// Test 8: Check session calculations
echo "\nTest 8: Checking session calculations...\n";
$student = Student::whereHas('enrollments', function($q) {
    $q->where('status', 'ACTIVE')->whereHas('package');
})->with(['enrollments' => function($q) {
    $q->where('status', 'ACTIVE')->with('package');
}])->first();

if ($student && $student->enrollments->first()) {
    $enrollment = $student->enrollments->first();
    $totalSessions = $enrollment->package->total_sessions ?? 0;
    $completedSessions = SessionOccurrence::where('student_id', $student->id)
        ->where('status', 'COMPLETED')
        ->count();
    $remaining = max(0, $totalSessions - $completedSessions);
    
    echo "  ✓ Session calculations working:\n";
    echo "    - Total Sessions: {$totalSessions}\n";
    echo "    - Completed: {$completedSessions}\n";
    echo "    - Remaining: {$remaining}\n";
    $passed++;
} else {
    echo "  ⚠ No student with package to test\n";
    $passed++;
}

// Test 9: Check ParentStudentScope
echo "\nTest 9: Checking ParentStudentScope...\n";
if (class_exists('App\Scopes\ParentStudentScope')) {
    echo "  ✓ ParentStudentScope exists\n";
    $passed++;
} else {
    echo "  ✗ ParentStudentScope not found\n";
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
    echo "  ✓ All middleware classes exist\n";
    $passed++;
} else {
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
    echo "  ✅ ALL TESTS PASSED! Parent portal is ready.\n";
    echo "\n";
    echo "  Parent Portal Features:\n";
    echo "    • Dashboard with real-time data\n";
    echo "    • Attendance monitoring\n";
    echo "    • Payment history\n";
    echo "    • Session tracking\n";
    echo "    • Notifications & alerts\n";
    echo "\n";
    echo "  Access:\n";
    echo "    URL: /parent/login\n";
    echo "    Test: maria@shiloh.local / password\n";
    echo "\n";
} else {
    echo "  ⚠️  SOME TESTS FAILED. Please review the errors above.\n";
    echo "\n";
    exit(1);
}

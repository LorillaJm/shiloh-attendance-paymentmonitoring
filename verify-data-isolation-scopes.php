<?php

/**
 * Verification script for data isolation scopes
 * 
 * This script tests that:
 * 1. SUPERADMIN and ADMIN can see all records
 * 2. PARENT can only see their children's records
 * 3. PARENT without guardian relationship sees no records
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\PaymentTransaction;
use App\Models\Guardian;
use Illuminate\Support\Facades\Auth;

echo "=== Data Isolation Scopes Verification ===\n\n";

// Get total counts without authentication
$totalStudents = Student::withoutGlobalScopes()->count();
$totalAttendance = AttendanceRecord::withoutGlobalScopes()->count();
$totalPayments = PaymentTransaction::withoutGlobalScopes()->count();

echo "Total records in database (without scopes):\n";
echo "- Students: {$totalStudents}\n";
echo "- Attendance Records: {$totalAttendance}\n";
echo "- Payment Transactions: {$totalPayments}\n\n";

// Test 1: SUPERADMIN sees all records
echo "--- Test 1: SUPERADMIN Access ---\n";
$superadmin = User::where('role', 'SUPERADMIN')->first();
if ($superadmin) {
    Auth::login($superadmin);
    
    $studentsCount = Student::count();
    $attendanceCount = AttendanceRecord::count();
    $paymentsCount = PaymentTransaction::count();
    
    echo "Logged in as: {$superadmin->name} (SUPERADMIN)\n";
    echo "- Students visible: {$studentsCount}\n";
    echo "- Attendance Records visible: {$attendanceCount}\n";
    echo "- Payment Transactions visible: {$paymentsCount}\n";
    
    if ($studentsCount === $totalStudents && $attendanceCount === $totalAttendance && $paymentsCount === $totalPayments) {
        echo "✓ SUPERADMIN can see all records\n\n";
    } else {
        echo "✗ SUPERADMIN scope not working correctly\n\n";
    }
    
    Auth::logout();
} else {
    echo "⚠ No SUPERADMIN user found\n\n";
}

// Test 2: ADMIN sees all records
echo "--- Test 2: ADMIN Access ---\n";
$admin = User::where('role', 'ADMIN')->first();
if ($admin) {
    Auth::login($admin);
    
    $studentsCount = Student::count();
    $attendanceCount = AttendanceRecord::count();
    $paymentsCount = PaymentTransaction::count();
    
    echo "Logged in as: {$admin->name} (ADMIN)\n";
    echo "- Students visible: {$studentsCount}\n";
    echo "- Attendance Records visible: {$attendanceCount}\n";
    echo "- Payment Transactions visible: {$paymentsCount}\n";
    
    if ($studentsCount === $totalStudents && $attendanceCount === $totalAttendance && $paymentsCount === $totalPayments) {
        echo "✓ ADMIN can see all records\n\n";
    } else {
        echo "✗ ADMIN scope not working correctly\n\n";
    }
    
    Auth::logout();
} else {
    echo "⚠ No ADMIN user found\n\n";
}

// Test 3: PARENT sees only their children's records
echo "--- Test 3: PARENT Access (with guardian relationship) ---\n";
$parent = User::where('role', 'PARENT')->whereHas('guardian')->first();
if ($parent && $parent->guardian) {
    Auth::login($parent);
    
    $guardian = $parent->guardian;
    $childrenCount = $guardian->students()->count();
    $childrenIds = $guardian->students()->pluck('students.id')->toArray();
    
    $studentsCount = Student::count();
    $attendanceCount = AttendanceRecord::count();
    $paymentsCount = PaymentTransaction::count();
    
    // Calculate expected counts
    $expectedAttendance = AttendanceRecord::withoutGlobalScopes()
        ->whereIn('student_id', $childrenIds)
        ->count();
    $expectedPayments = PaymentTransaction::withoutGlobalScopes()
        ->whereHas('enrollment', function($q) use ($childrenIds) {
            $q->whereIn('student_id', $childrenIds);
        })
        ->count();
    
    echo "Logged in as: {$parent->name} (PARENT)\n";
    echo "Guardian has {$childrenCount} child(ren)\n";
    echo "- Students visible: {$studentsCount} (expected: {$childrenCount})\n";
    echo "- Attendance Records visible: {$attendanceCount} (expected: {$expectedAttendance})\n";
    echo "- Payment Transactions visible: {$paymentsCount} (expected: {$expectedPayments})\n";
    
    $allCorrect = true;
    if ($studentsCount === $childrenCount) {
        echo "✓ Student scope working correctly\n";
    } else {
        echo "✗ Student scope not working correctly\n";
        $allCorrect = false;
    }
    
    if ($attendanceCount === $expectedAttendance) {
        echo "✓ Attendance scope working correctly\n";
    } else {
        echo "✗ Attendance scope not working correctly\n";
        $allCorrect = false;
    }
    
    if ($paymentsCount === $expectedPayments) {
        echo "✓ Payment scope working correctly\n";
    } else {
        echo "✗ Payment scope not working correctly\n";
        $allCorrect = false;
    }
    
    if ($allCorrect) {
        echo "✓ PARENT can only see their children's records\n\n";
    } else {
        echo "✗ PARENT scope not working correctly\n\n";
    }
    
    Auth::logout();
} else {
    echo "⚠ No PARENT user with guardian relationship found\n\n";
}

// Test 4: PARENT without guardian relationship sees no records
echo "--- Test 4: PARENT Access (without guardian relationship) ---\n";
$parentNoGuardian = User::where('role', 'PARENT')->whereDoesntHave('guardian')->first();
if ($parentNoGuardian) {
    Auth::login($parentNoGuardian);
    
    $studentsCount = Student::count();
    $attendanceCount = AttendanceRecord::count();
    $paymentsCount = PaymentTransaction::count();
    
    echo "Logged in as: {$parentNoGuardian->name} (PARENT without guardian)\n";
    echo "- Students visible: {$studentsCount}\n";
    echo "- Attendance Records visible: {$attendanceCount}\n";
    echo "- Payment Transactions visible: {$paymentsCount}\n";
    
    if ($studentsCount === 0 && $attendanceCount === 0 && $paymentsCount === 0) {
        echo "✓ PARENT without guardian sees no records\n\n";
    } else {
        echo "✗ PARENT without guardian scope not working correctly\n\n";
    }
    
    Auth::logout();
} else {
    echo "⚠ No PARENT user without guardian relationship found\n\n";
}

echo "=== Verification Complete ===\n";

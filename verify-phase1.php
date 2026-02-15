<?php

/**
 * Phase 1 Verification Script
 * Run with: php verify-phase1.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\Package;
use Illuminate\Support\Facades\DB;

echo "================================================================================\n";
echo "PHASE 1 VERIFICATION: Students & Packages Modules\n";
echo "================================================================================\n\n";

$passed = 0;
$failed = 0;

// Test 1: Check indexes exist
echo "Test 1: Checking performance indexes...\n";
try {
    $indexes = DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'students' AND indexname = 'students_name_search_index'");
    if (count($indexes) > 0) {
        echo "  ✓ students_name_search_index exists\n";
        $passed++;
    } else {
        echo "  ✗ students_name_search_index missing\n";
        $failed++;
    }
    
    $indexes = DB::select("SELECT indexname FROM pg_indexes WHERE tablename = 'students' AND indexname = 'students_guardian_contact_index'");
    if (count($indexes) > 0) {
        echo "  ✓ students_guardian_contact_index exists\n";
        $passed++;
    } else {
        echo "  ✗ students_guardian_contact_index missing\n";
        $failed++;
    }
} catch (\Exception $e) {
    echo "  ✗ Error checking indexes: " . $e->getMessage() . "\n";
    $failed += 2;
}

// Test 2: Check constraints exist
echo "\nTest 2: Checking database constraints...\n";
try {
    $constraints = DB::select("SELECT constraint_name FROM information_schema.table_constraints WHERE table_name = 'students' AND constraint_name = 'students_sex_check'");
    if (count($constraints) > 0) {
        echo "  ✓ students_sex_check constraint exists\n";
        $passed++;
    } else {
        echo "  ✗ students_sex_check constraint missing\n";
        $failed++;
    }
    
    $constraints = DB::select("SELECT constraint_name FROM information_schema.table_constraints WHERE table_name = 'students' AND constraint_name = 'students_status_check'");
    if (count($constraints) > 0) {
        echo "  ✓ students_status_check constraint exists\n";
        $passed++;
    } else {
        echo "  ✗ students_status_check constraint missing\n";
        $failed++;
    }
    
    $constraints = DB::select("SELECT constraint_name FROM information_schema.table_constraints WHERE table_name = 'packages' AND constraint_name = 'packages_total_fee_check'");
    if (count($constraints) > 0) {
        echo "  ✓ packages_total_fee_check constraint exists\n";
        $passed++;
    } else {
        echo "  ✗ packages_total_fee_check constraint missing\n";
        $failed++;
    }
} catch (\Exception $e) {
    echo "  ✗ Error checking constraints: " . $e->getMessage() . "\n";
    $failed += 3;
}

// Test 3: Test student number generator
echo "\nTest 3: Testing student number generator...\n";
try {
    $student1 = Student::create([
        'first_name' => 'VerifyTest1',
        'last_name' => 'Student',
        'guardian_name' => 'Test Guardian',
        'guardian_contact' => '+639123456789',
        'status' => 'ACTIVE',
    ]);
    
    $student2 = Student::create([
        'first_name' => 'VerifyTest2',
        'last_name' => 'Student',
        'guardian_name' => 'Test Guardian',
        'guardian_contact' => '+639123456789',
        'status' => 'ACTIVE',
    ]);
    
    if ($student1->student_no !== $student2->student_no) {
        echo "  ✓ Student numbers are unique: {$student1->student_no} vs {$student2->student_no}\n";
        $passed++;
    } else {
        echo "  ✗ Student numbers are duplicate!\n";
        $failed++;
    }
    
    if (preg_match('/^SHILOH-\d{4}-\d{4}$/', $student1->student_no)) {
        echo "  ✓ Student number format correct: {$student1->student_no}\n";
        $passed++;
    } else {
        echo "  ✗ Student number format incorrect: {$student1->student_no}\n";
        $failed++;
    }
    
    // Clean up
    $student1->delete();
    $student2->delete();
} catch (\Exception $e) {
    echo "  ✗ Error testing student creation: " . $e->getMessage() . "\n";
    $failed += 2;
}

// Test 4: Test constraint enforcement
echo "\nTest 4: Testing constraint enforcement...\n";
try {
    DB::statement("INSERT INTO students (student_no, first_name, last_name, sex, status, created_at, updated_at) VALUES ('VERIFY-TEST-001', 'Test', 'User', 'Invalid', 'ACTIVE', NOW(), NOW())");
    echo "  ✗ Invalid sex was accepted (constraint not working)\n";
    $failed++;
    DB::statement("DELETE FROM students WHERE student_no = 'VERIFY-TEST-001'");
} catch (\Exception $e) {
    if (strpos($e->getMessage(), 'students_sex_check') !== false) {
        echo "  ✓ Invalid sex rejected by constraint\n";
        $passed++;
    } else {
        echo "  ✗ Wrong error: " . $e->getMessage() . "\n";
        $failed++;
    }
}

try {
    DB::statement("INSERT INTO packages (name, total_fee, downpayment_percent, installment_months, created_at, updated_at) VALUES ('Verify Test Package', 0, 25, 3, NOW(), NOW())");
    echo "  ✗ Zero total_fee was accepted (constraint not working)\n";
    $failed++;
    DB::statement("DELETE FROM packages WHERE name = 'Verify Test Package'");
} catch (\Exception $e) {
    if (strpos($e->getMessage(), 'packages_total_fee_check') !== false) {
        echo "  ✓ Zero total_fee rejected by constraint\n";
        $passed++;
    } else {
        echo "  ✗ Wrong error: " . $e->getMessage() . "\n";
        $failed++;
    }
}

// Test 5: Test package calculations
echo "\nTest 5: Testing package calculations...\n";
try {
    $package = Package::create([
        'name' => 'Verify Test Package ' . time(),
        'total_fee' => 10000.00,
        'downpayment_percent' => 25.00,
        'installment_months' => 3,
    ]);
    
    if ($package->downpayment_amount == 2500.00) {
        echo "  ✓ Downpayment calculation correct: ₱2,500.00\n";
        $passed++;
    } else {
        echo "  ✗ Downpayment calculation wrong: ₱" . number_format($package->downpayment_amount, 2) . "\n";
        $failed++;
    }
    
    if ($package->monthly_installment == 2500.00) {
        echo "  ✓ Monthly installment calculation correct: ₱2,500.00\n";
        $passed++;
    } else {
        echo "  ✗ Monthly installment calculation wrong: ₱" . number_format($package->monthly_installment, 2) . "\n";
        $failed++;
    }
    
    // Clean up
    $package->delete();
} catch (\Exception $e) {
    echo "  ✗ Error testing package: " . $e->getMessage() . "\n";
    $failed += 2;
}

// Summary
echo "\n================================================================================\n";
echo "VERIFICATION SUMMARY\n";
echo "================================================================================\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Total:  " . ($passed + $failed) . "\n";
echo "\n";

if ($failed === 0) {
    echo "✅ ALL TESTS PASSED - Phase 1 is production ready!\n";
    exit(0);
} else {
    echo "⚠️  SOME TESTS FAILED - Please review the issues above\n";
    exit(1);
}

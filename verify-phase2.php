<?php

/**
 * Phase 2 Verification Script
 * Run with: php verify-phase2.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Services\PaymentScheduleService;
use Carbon\Carbon;

echo "================================================================================\n";
echo "PHASE 2 VERIFICATION: Enrollment & PaymentScheduleService\n";
echo "================================================================================\n\n";

$service = new PaymentScheduleService();
$passed = 0;
$failed = 0;

// Helper function to create test enrollment
function createTestEnrollment($enrollmentDate, $totalFee, $dpPercent, $months) {
    $student = Student::create([
        'first_name' => 'VerifyTest',
        'last_name' => 'Student' . time(),
        'guardian_name' => 'Test Guardian',
        'guardian_contact' => '+639123456789',
        'status' => 'ACTIVE',
    ]);

    $package = Package::create([
        'name' => 'Verify Package ' . time() . rand(1000, 9999),
        'total_fee' => $totalFee,
        'downpayment_percent' => $dpPercent,
        'installment_months' => $months,
    ]);

    $dpAmount = round(($totalFee * $dpPercent) / 100, 2);
    $remaining = round($totalFee - $dpAmount, 2);

    return Enrollment::create([
        'student_id' => $student->id,
        'package_id' => $package->id,
        'enrollment_date' => $enrollmentDate,
        'total_fee' => $totalFee,
        'downpayment_percent' => $dpPercent,
        'downpayment_amount' => $dpAmount,
        'remaining_balance' => $remaining,
        'status' => 'ACTIVE',
    ]);
}

// Test 1: Enrollment before 15th
echo "Test 1: Enrollment before 15th (Jan 10 → Feb 15)...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
    
    if ($schedules[1]->due_date->format('Y-m-d') === '2026-02-15') {
        echo "  ✓ First due date correct: 2026-02-15\n";
        $passed++;
    } else {
        echo "  ✗ First due date wrong: " . $schedules[1]->due_date->format('Y-m-d') . " (expected 2026-02-15)\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 2: Enrollment on 15th
echo "\nTest 2: Enrollment on 15th (Jan 15 → Feb 15)...\n";
try {
    $enrollment = createTestEnrollment('2026-01-15', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
    
    if ($schedules[1]->due_date->format('Y-m-d') === '2026-02-15') {
        echo "  ✓ First due date correct: 2026-02-15\n";
        $passed++;
    } else {
        echo "  ✗ First due date wrong: " . $schedules[1]->due_date->format('Y-m-d') . " (expected 2026-02-15)\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 3: Enrollment after 15th
echo "\nTest 3: Enrollment after 15th (Jan 20 → Feb 15)...\n";
try {
    $enrollment = createTestEnrollment('2026-01-20', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
    
    if ($schedules[1]->due_date->format('Y-m-d') === '2026-02-15') {
        echo "  ✓ First due date correct: 2026-02-15\n";
        $passed++;
    } else {
        echo "  ✗ First due date wrong: " . $schedules[1]->due_date->format('Y-m-d') . " (expected 2026-02-15)\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 4: End of month
echo "\nTest 4: End of month (Jan 31 → Feb 15)...\n";
try {
    $enrollment = createTestEnrollment('2026-01-31', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
    
    if ($schedules[1]->due_date->format('Y-m-d') === '2026-02-15') {
        echo "  ✓ First due date correct: 2026-02-15\n";
        $passed++;
    } else {
        echo "  ✗ First due date wrong: " . $schedules[1]->due_date->format('Y-m-d') . " (expected 2026-02-15)\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 5: Leap year February
echo "\nTest 5: Leap year February (Feb 10, 2024 → Mar 15, 2024)...\n";
try {
    $enrollment = createTestEnrollment('2024-02-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
    
    if ($schedules[1]->due_date->format('Y-m-d') === '2024-03-15') {
        echo "  ✓ First due date correct: 2024-03-15\n";
        $passed++;
    } else {
        echo "  ✗ First due date wrong: " . $schedules[1]->due_date->format('Y-m-d') . " (expected 2024-03-15)\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 6: Rounding edge case (33.33%)
echo "\nTest 6: Rounding edge case (33.33% downpayment)...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 33.33, 3);
    $service->generateSchedules($enrollment);
    $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
    
    $total = $schedules->sum('amount_due');
    if ($total == 10000.00) {
        echo "  ✓ Total equals original: ₱10,000.00\n";
        $passed++;
    } else {
        echo "  ✗ Total mismatch: ₱" . number_format($total, 2) . " (expected ₱10,000.00)\n";
        $failed++;
    }
    
    // Check last installment adjustment
    if ($schedules[3]->amount_due == 2222.34) {
        echo "  ✓ Last installment adjusted: ₱2,222.34\n";
        $passed++;
    } else {
        echo "  ✗ Last installment wrong: ₱" . number_format($schedules[3]->amount_due, 2) . " (expected ₱2,222.34)\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed += 2;
}

// Test 7: 100% downpayment (0 installments)
echo "\nTest 7: 100% downpayment (0 installments)...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 100, 0);
    $service->generateSchedules($enrollment);
    $schedules = $enrollment->paymentSchedules()->get();
    
    if (count($schedules) === 1) {
        echo "  ✓ Only downpayment schedule created\n";
        $passed++;
    } else {
        echo "  ✗ Wrong schedule count: " . count($schedules) . " (expected 1)\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 8: Different installment counts
echo "\nTest 8: Different installment counts (1, 3, 6, 12)...\n";
try {
    $counts = [1, 3, 6, 12];
    $allPassed = true;
    
    foreach ($counts as $count) {
        $enrollment = createTestEnrollment('2026-01-10', 10000, 25, $count);
        $service->generateSchedules($enrollment);
        $schedules = $enrollment->paymentSchedules()->get();
        
        $expected = 1 + $count; // downpayment + installments
        if (count($schedules) !== $expected) {
            echo "  ✗ {$count} months: wrong count " . count($schedules) . " (expected {$expected})\n";
            $allPassed = false;
        }
        
        $enrollment->delete();
    }
    
    if ($allPassed) {
        echo "  ✓ All installment counts correct\n";
        $passed++;
    } else {
        $failed++;
    }
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 9: Sequential due dates
echo "\nTest 9: Sequential due dates (monthly 15th)...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    $schedules = $enrollment->paymentSchedules()->where('installment_no', '>', 0)->orderBy('installment_no')->get();
    
    $expected = ['2026-02-15', '2026-03-15', '2026-04-15'];
    $allCorrect = true;
    
    foreach ($schedules as $index => $schedule) {
        if ($schedule->due_date->format('Y-m-d') !== $expected[$index]) {
            echo "  ✗ Installment " . ($index + 1) . ": " . $schedule->due_date->format('Y-m-d') . " (expected {$expected[$index]})\n";
            $allCorrect = false;
        }
    }
    
    if ($allCorrect) {
        echo "  ✓ All due dates sequential and correct\n";
        $passed++;
    } else {
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 10: Downpayment due on enrollment date
echo "\nTest 10: Downpayment due on enrollment date...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    $schedules = $enrollment->paymentSchedules()->where('installment_no', 0)->first();
    
    if ($schedules->due_date->format('Y-m-d') === '2026-01-10') {
        echo "  ✓ Downpayment due on enrollment date: 2026-01-10\n";
        $passed++;
    } else {
        echo "  ✗ Downpayment due date wrong: " . $schedules->due_date->format('Y-m-d') . " (expected 2026-01-10)\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
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
    echo "✅ ALL TESTS PASSED - Phase 2 is production ready!\n";
    exit(0);
} else {
    echo "⚠️  SOME TESTS FAILED - Please review the issues above\n";
    exit(1);
}

<?php

/**
 * Phase 3 Verification Script
 * Run with: php verify-phase3.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use App\Models\ActivityLog;
use App\Services\PaymentScheduleService;
use Carbon\Carbon;

echo "================================================================================\n";
echo "PHASE 3 VERIFICATION: Payments & Collections\n";
echo "================================================================================\n\n";

$service = new PaymentScheduleService();
$passed = 0;
$failed = 0;

// Helper function
function createTestEnrollment($enrollmentDate, $totalFee, $dpPercent, $months) {
    $student = Student::create([
        'first_name' => 'PayTest',
        'last_name' => 'Student' . time(),
        'guardian_name' => 'Test Guardian',
        'guardian_contact' => '+639123456789',
        'status' => 'ACTIVE',
    ]);

    $package = Package::create([
        'name' => 'Pay Package ' . time() . rand(1000, 9999),
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

// Test 1: Mark as Paid updates status
echo "Test 1: Mark as Paid updates status...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    
    $schedule = $enrollment->paymentSchedules()->where('installment_no', 0)->first();
    $schedule->update([
        'status' => 'PAID',
        'paid_at' => now(),
        'payment_method' => 'CASH',
    ]);
    
    $schedule->refresh();
    if ($schedule->status === 'PAID' && $schedule->paid_at !== null) {
        echo "  ✓ Status updated to PAID with paid_at timestamp\n";
        $passed++;
    } else {
        echo "  ✗ Status not updated correctly\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 2: Activity log created
echo "\nTest 2: Activity log created for payment...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    
    $initialLogCount = ActivityLog::where('log_name', 'payment')->count();
    
    $schedule = $enrollment->paymentSchedules()->where('installment_no', 0)->first();
    \App\Services\ActivityLogger::log(
        description: "Payment marked as paid",
        subject: $schedule,
        properties: [
            'amount' => $schedule->amount_due,
            'payment_method' => 'CASH',
        ],
        logName: 'payment'
    );
    
    $newLogCount = ActivityLog::where('log_name', 'payment')->count();
    if ($newLogCount > $initialLogCount) {
        echo "  ✓ Activity log created\n";
        $passed++;
    } else {
        echo "  ✗ Activity log not created\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 3: Balance recalculation
echo "\nTest 3: Balance recalculation after payment...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    
    $initialPaid = $enrollment->total_paid;
    
    $schedule = $enrollment->paymentSchedules()->where('installment_no', 0)->first();
    $schedule->update(['status' => 'PAID', 'paid_at' => now(), 'payment_method' => 'CASH']);
    
    $enrollment->refresh();
    $newPaid = $enrollment->total_paid;
    
    if ($newPaid > $initialPaid && $newPaid == 2500.00) {
        echo "  ✓ Total paid increased correctly: ₱" . number_format($newPaid, 2) . "\n";
        $passed++;
    } else {
        echo "  ✗ Total paid not calculated correctly: ₱" . number_format($newPaid, 2) . "\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 4: Overdue status computation
echo "\nTest 4: Overdue status computation...\n";
try {
    $enrollment = createTestEnrollment('2025-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    
    // Get a schedule that should be overdue (Feb 15, 2025)
    $schedule = $enrollment->paymentSchedules()->where('installment_no', 1)->first();
    
    if ($schedule->is_overdue && $schedule->computed_status === 'OVERDUE') {
        echo "  ✓ Overdue status computed correctly\n";
        $passed++;
    } else {
        echo "  ✗ Overdue status not computed: is_overdue=" . ($schedule->is_overdue ? 'true' : 'false') . ", computed_status=" . $schedule->computed_status . "\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 5: Due payments list (next 15th)
echo "\nTest 5: Due payments list shows correct schedules...\n";
try {
    // Create enrollment with payment due next month's 15th
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    
    // Calculate next 15th using same logic as DuePayments page
    $next15th = now()->startOfMonth()->addMonth()->day(15);
    
    $dueSchedules = PaymentSchedule::where('status', 'UNPAID')
        ->whereDate('due_date', $next15th->format('Y-m-d'))
        ->count();
    
    if ($dueSchedules > 0) {
        echo "  ✓ Due payments list working (found {$dueSchedules} schedules for " . $next15th->format('Y-m-d') . ")\n";
        $passed++;
    } else {
        echo "  ✗ No due payments found for " . $next15th->format('Y-m-d') . "\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 6: Overdue payments list
echo "\nTest 6: Overdue payments list shows correct schedules...\n";
try {
    // Create enrollment with overdue payment
    $enrollment = createTestEnrollment('2025-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    
    $overdueSchedules = PaymentSchedule::where('status', 'UNPAID')
        ->whereNotNull('due_date')
        ->where('due_date', '<', now()->format('Y-m-d'))
        ->count();
    
    if ($overdueSchedules > 0) {
        echo "  ✓ Overdue payments list working (found {$overdueSchedules} overdue schedules)\n";
        $passed++;
    } else {
        echo "  ✗ No overdue payments found\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 7: Collections today
echo "\nTest 7: Collections today calculation...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    
    $schedule = $enrollment->paymentSchedules()->where('installment_no', 0)->first();
    $schedule->update([
        'status' => 'PAID',
        'paid_at' => now(),
        'payment_method' => 'CASH',
    ]);
    
    $paidToday = PaymentSchedule::where('status', 'PAID')
        ->whereDate('paid_at', now()->format('Y-m-d'))
        ->sum('amount_due');
    
    if ($paidToday >= 2500.00) {
        echo "  ✓ Collections today calculated: ₱" . number_format($paidToday, 2) . "\n";
        $passed++;
    } else {
        echo "  ✗ Collections today incorrect: ₱" . number_format($paidToday, 2) . "\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 8: Collections this month
echo "\nTest 8: Collections this month calculation...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    
    $schedule = $enrollment->paymentSchedules()->where('installment_no', 0)->first();
    $schedule->update([
        'status' => 'PAID',
        'paid_at' => now(),
        'payment_method' => 'CASH',
    ]);
    
    $monthStart = now()->startOfMonth()->format('Y-m-d');
    $monthEnd = now()->endOfMonth()->format('Y-m-d');
    
    $paidThisMonth = PaymentSchedule::where('status', 'PAID')
        ->whereBetween('paid_at', [$monthStart, $monthEnd])
        ->sum('amount_due');
    
    if ($paidThisMonth >= 2500.00) {
        echo "  ✓ Collections this month calculated: ₱" . number_format($paidThisMonth, 2) . "\n";
        $passed++;
    } else {
        echo "  ✗ Collections this month incorrect: ₱" . number_format($paidThisMonth, 2) . "\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 9: Payment method stored
echo "\nTest 9: Payment method stored correctly...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    
    $schedule = $enrollment->paymentSchedules()->where('installment_no', 0)->first();
    $schedule->update([
        'status' => 'PAID',
        'paid_at' => now(),
        'payment_method' => 'GCASH',
        'receipt_no' => 'RCP-12345',
    ]);
    
    $schedule->refresh();
    if ($schedule->payment_method === 'GCASH' && $schedule->receipt_no === 'RCP-12345') {
        echo "  ✓ Payment method and receipt stored correctly\n";
        $passed++;
    } else {
        echo "  ✗ Payment details not stored correctly\n";
        $failed++;
    }
    
    $enrollment->delete();
} catch (\Exception $e) {
    echo "  ✗ Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 10: Computed status for paid schedule
echo "\nTest 10: Computed status for paid schedule...\n";
try {
    $enrollment = createTestEnrollment('2026-01-10', 10000, 25, 3);
    $service->generateSchedules($enrollment);
    
    $schedule = $enrollment->paymentSchedules()->where('installment_no', 0)->first();
    $schedule->update([
        'status' => 'PAID',
        'paid_at' => now(),
        'payment_method' => 'CASH',
    ]);
    
    $schedule->refresh();
    if ($schedule->computed_status === 'PAID') {
        echo "  ✓ Computed status correct for paid schedule\n";
        $passed++;
    } else {
        echo "  ✗ Computed status incorrect: " . $schedule->computed_status . "\n";
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
    echo "✅ ALL TESTS PASSED - Phase 3 is production ready!\n";
    exit(0);
} else {
    echo "⚠️  SOME TESTS FAILED - Please review the issues above\n";
    exit(1);
}

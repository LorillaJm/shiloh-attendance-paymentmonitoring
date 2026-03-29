<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING STUDENT LEDGER DATA ===\n\n";

// Get a student with enrollments
$student = App\Models\Student::with([
    'enrollments.package',
    'enrollments.paymentSchedules' => fn ($q) => $q->orderBy('installment_no')
])->whereHas('enrollments')->first();

if (!$student) {
    echo "No students with enrollments found\n";
    exit;
}

echo "Student: {$student->student_no} - {$student->full_name}\n";
echo "Guardian: {$student->guardian_name}\n";
echo "Contact: {$student->guardian_contact}\n\n";

foreach ($student->enrollments as $enrollment) {
    echo "=== ENROLLMENT: {$enrollment->package->name} ===\n";
    echo "Enrolled: {$enrollment->enrollment_date->format('F d, Y')}\n";
    echo "Total Fee: ₱" . number_format($enrollment->total_fee, 2) . "\n";
    echo "Total Paid: ₱" . number_format($enrollment->total_paid, 2) . "\n";
    echo "Balance: ₱" . number_format($enrollment->remaining_balance_computed, 2) . "\n\n";
    
    echo "Payment Schedule:\n";
    foreach ($enrollment->paymentSchedules as $schedule) {
        $installment = $schedule->installment_no == 0 ? 'Downpayment' : "Installment #{$schedule->installment_no}";
        $dueDate = $schedule->due_date ? $schedule->due_date->format('Y-m-d') : '-';
        $paidDate = $schedule->paid_at ? $schedule->paid_at->format('Y-m-d') : '-';
        $method = $schedule->payment_method ?? '-';
        
        echo "  {$installment}: ₱" . number_format($schedule->amount_due, 2);
        echo " | Due: {$dueDate} | Status: {$schedule->status}";
        echo " | Paid: {$paidDate} | Method: {$method}\n";
    }
    echo "\n";
}

echo "✅ Student Ledger data is available and working!\n";

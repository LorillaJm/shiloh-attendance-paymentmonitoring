<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$enrollment = App\Models\Enrollment::find(11);

if (!$enrollment) {
    echo "Enrollment 11 not found\n";
    exit;
}

echo "Fixing Enrollment #11...\n\n";

// Find paid schedules without transactions
$paidSchedules = $enrollment->paymentSchedules()->where('status', 'PAID')->get();

echo "Found {$paidSchedules->count()} paid schedule(s)\n";

foreach ($paidSchedules as $schedule) {
    // Check if transaction already exists
    $existingTransaction = App\Models\PaymentTransaction::where('payment_schedule_id', $schedule->id)->first();
    
    if ($existingTransaction) {
        echo "- Schedule #{$schedule->installment_no} already has a transaction\n";
        continue;
    }
    
    // Create missing transaction
    $transaction = App\Models\PaymentTransaction::create([
        'enrollment_id' => $schedule->enrollment_id,
        'payment_schedule_id' => $schedule->id,
        'transaction_date' => $schedule->paid_at ?? now(),
        'type' => 'PAYMENT',
        'amount' => $schedule->amount_due,
        'payment_method' => $schedule->payment_method ?? 'CASH',
        'receipt_no' => $schedule->receipt_no,
        'remarks' => 'Retroactively created transaction for existing paid schedule',
        'processed_by_user_id' => 1, // Admin user
    ]);
    
    echo "✓ Created transaction for Schedule #{$schedule->installment_no} - ₱" . number_format($schedule->amount_due, 2) . "\n";
}

// Verify the fix
$enrollment->refresh();
echo "\n=== VERIFICATION ===\n";
echo "Total Paid: ₱" . number_format($enrollment->total_paid, 2) . "\n";
echo "Balance Due: ₱" . number_format($enrollment->remaining_balance_computed, 2) . "\n";
echo "\n✅ Fix completed!\n";

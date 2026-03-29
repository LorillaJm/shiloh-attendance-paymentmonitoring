<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIXING ALL MISSING PAYMENT TRANSACTIONS ===\n\n";

// Find all paid schedules without transactions
$paidSchedules = App\Models\PaymentSchedule::where('status', 'PAID')
    ->whereDoesntHave('transactions')
    ->with('enrollment.student')
    ->get();

echo "Found {$paidSchedules->count()} paid schedule(s) without transactions\n\n";

if ($paidSchedules->isEmpty()) {
    echo "✅ All paid schedules have transactions!\n";
    exit;
}

$fixed = 0;
$errors = 0;

foreach ($paidSchedules as $schedule) {
    try {
        $transaction = App\Models\PaymentTransaction::create([
            'enrollment_id' => $schedule->enrollment_id,
            'payment_schedule_id' => $schedule->id,
            'transaction_date' => $schedule->paid_at ?? now(),
            'type' => 'PAYMENT',
            'amount' => $schedule->amount_due,
            'payment_method' => $schedule->payment_method ?? 'CASH',
            'receipt_no' => $schedule->receipt_no,
            'remarks' => 'Retroactively created transaction for existing paid schedule',
            'processed_by_user_id' => 1,
        ]);
        
        $installmentLabel = $schedule->installment_no == 0 ? 'Downpayment' : "Installment #{$schedule->installment_no}";
        $studentName = $schedule->enrollment->student->full_name ?? 'Unknown';
        
        echo "✓ Fixed: {$studentName} - {$installmentLabel} - ₱" . number_format($schedule->amount_due, 2) . "\n";
        $fixed++;
        
    } catch (Exception $e) {
        echo "✗ Error fixing schedule #{$schedule->id}: {$e->getMessage()}\n";
        $errors++;
    }
}

echo "\n=== SUMMARY ===\n";
echo "Fixed: {$fixed}\n";
echo "Errors: {$errors}\n";
echo "\n✅ Done!\n";

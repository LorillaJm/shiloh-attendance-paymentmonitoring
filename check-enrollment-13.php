<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$enrollment = App\Models\Enrollment::find(11);

if (!$enrollment) {
    echo "Enrollment 11 not found\n";
    exit;
}

echo "=== ENROLLMENT #11 ===\n";
echo "Student: {$enrollment->student->full_name}\n";
echo "Package: {$enrollment->package->name}\n";
echo "Total Fee: ₱" . number_format($enrollment->total_fee, 2) . "\n\n";

echo "=== PAYMENT SCHEDULES ===\n";
$schedules = $enrollment->paymentSchedules;
echo "Total Schedules: {$schedules->count()}\n";
foreach ($schedules as $schedule) {
    $label = $schedule->installment_no == 0 ? 'Downpayment' : "Installment #{$schedule->installment_no}";
    echo "- {$label}: ₱{$schedule->amount_due} - {$schedule->status}";
    if ($schedule->paid_at) {
        echo " (Paid: {$schedule->paid_at})";
    }
    echo "\n";
}

echo "\n=== PAYMENT TRANSACTIONS ===\n";
$transactions = $enrollment->paymentTransactions;
echo "Total Transactions: {$transactions->count()}\n";
foreach ($transactions as $transaction) {
    echo "- {$transaction->transaction_date}: {$transaction->type} - ₱{$transaction->amount}\n";
}

echo "\n=== CALCULATED TOTALS ===\n";
echo "Total Paid (from attribute): ₱" . number_format($enrollment->total_paid, 2) . "\n";
echo "Total Paid (direct query): ₱" . number_format($enrollment->paymentTransactions()->where('type', 'PAYMENT')->sum('amount'), 2) . "\n";
echo "Remaining Balance: ₱" . number_format($enrollment->remaining_balance_computed, 2) . "\n";

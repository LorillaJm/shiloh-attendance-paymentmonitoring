<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$schedules = App\Models\PaymentSchedule::where('enrollment_id', 11)
    ->orderBy('id')
    ->get();

echo "=== PAYMENT SCHEDULES FOR ENROLLMENT #11 ===\n\n";

foreach ($schedules as $schedule) {
    echo "ID: {$schedule->id}\n";
    echo "  Installment No: '{$schedule->installment_no}'\n";
    echo "  Amount Due: ₱{$schedule->amount_due}\n";
    echo "  Status: {$schedule->status}\n";
    echo "  Due Date: {$schedule->due_date}\n";
    echo "  Paid At: " . ($schedule->paid_at ?? 'NULL') . "\n";
    echo "\n";
}

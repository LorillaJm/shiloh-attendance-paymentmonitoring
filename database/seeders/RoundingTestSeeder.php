<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Services\PaymentScheduleService;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RoundingTestSeeder extends Seeder
{
    /**
     * Test rounding with amounts that don't divide evenly.
     */
    public function run(): void
    {
        $student = Student::firstOrCreate(
            ['student_no' => 'TEST-2026-002'],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'birthdate' => '2011-05-20',
                'sex' => 'Female',
                'address' => '456 Test Avenue, Quezon City',
                'guardian_name' => 'Pedro Santos',
                'guardian_contact' => '09181234567',
                'status' => 'ACTIVE',
            ]
        );

        // Package with amount that doesn't divide evenly
        $package = Package::firstOrCreate(
            ['name' => 'Package Rounding Test'],
            [
                'total_fee' => 10000.00,
                'downpayment_percent' => 20.00,
                'installment_months' => 3,
                'description' => 'Test rounding: 10000 total, 20% DP, 3 months',
            ]
        );

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'package_id' => $package->id,
            'enrollment_date' => Carbon::parse('2026-02-05'),
            'total_fee' => $package->total_fee,
            'downpayment_percent' => $package->downpayment_percent,
            'downpayment_amount' => ($package->total_fee * $package->downpayment_percent) / 100,
            'remaining_balance' => $package->total_fee - (($package->total_fee * $package->downpayment_percent) / 100),
            'status' => 'ACTIVE',
        ]);

        $paymentScheduleService = app(PaymentScheduleService::class);
        $paymentScheduleService->generateSchedules($enrollment);

        $this->command->info('=== Rounding Test Case ===');
        $this->command->info("Student: {$student->full_name} ({$student->student_no})");
        $this->command->info("Package: {$package->name}");
        $this->command->info("Enrollment Date: {$enrollment->enrollment_date->format('Y-m-d')}");
        $this->command->info("Total Fee: ₱" . number_format($enrollment->total_fee, 2));
        $this->command->info("Downpayment ({$enrollment->downpayment_percent}%): ₱" . number_format($enrollment->downpayment_amount, 2));
        $this->command->info("Remaining Balance: ₱" . number_format($enrollment->remaining_balance, 2));
        $this->command->info('');
        $this->command->info('Calculation: ₱8,000.00 / 3 = ₱2,666.666...');
        $this->command->info('Base installment (floor): ₱2,666.66');
        $this->command->info('Total base (2,666.66 × 3): ₱7,999.98');
        $this->command->info('Adjustment needed: ₱0.02');
        $this->command->info('');
        $this->command->info('=== Payment Schedule ===');

        foreach ($enrollment->paymentSchedules()->orderBy('installment_no')->get() as $schedule) {
            $label = $schedule->installment_no == 0 ? 'Downpayment' : "Installment #{$schedule->installment_no}";
            $dueDate = $schedule->due_date ? $schedule->due_date->format('Y-m-d (M d)') : 'N/A';
            $this->command->info("{$label}: ₱" . number_format($schedule->amount_due, 2) . " - Due: {$dueDate}");
        }

        $totalScheduled = $enrollment->paymentSchedules()->sum('amount_due');
        $this->command->info('');
        $this->command->info("Total Scheduled: ₱" . number_format($totalScheduled, 2));
        $this->command->info("Expected Total: ₱" . number_format($enrollment->total_fee, 2));
        $this->command->info("Match: " . ($totalScheduled == $enrollment->total_fee ? '✓ YES' : '✗ NO'));
        
        // Verify installment amounts
        $installments = $enrollment->paymentSchedules()->where('installment_no', '>', 0)->orderBy('installment_no')->get();
        $this->command->info('');
        $this->command->info('Verification:');
        $this->command->info("Installment #1: ₱{$installments[0]->amount_due}");
        $this->command->info("Installment #2: ₱{$installments[1]->amount_due}");
        $this->command->info("Installment #3: ₱{$installments[2]->amount_due} (adjusted +₱0.02)");
    }
}

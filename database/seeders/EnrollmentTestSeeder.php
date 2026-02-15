<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Services\PaymentScheduleService;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EnrollmentTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Test Case: Package C total 15000, DP 25%, enrollment Jan 10
     * Expected: DP 3750 + 3 installments of 3750 due Feb 15, Mar 15, Apr 15
     */
    public function run(): void
    {
        // Create or find a test student
        $student = Student::firstOrCreate(
            ['student_no' => 'TEST-2026-001'],
            [
                'first_name' => 'Juan',
                'last_name' => 'Dela Cruz',
                'birthdate' => '2010-01-15',
                'sex' => 'Male',
                'address' => '123 Test Street, Manila',
                'guardian_name' => 'Maria Dela Cruz',
                'guardian_contact' => '09171234567',
                'status' => 'ACTIVE',
            ]
        );

        // Create Package C
        $packageC = Package::firstOrCreate(
            ['name' => 'Package C'],
            [
                'total_fee' => 15000.00,
                'downpayment_percent' => 25.00,
                'installment_months' => 3,
                'description' => 'Test Package C - 15000 total, 25% DP, 3 months',
            ]
        );

        // Create enrollment on Jan 10, 2026
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'package_id' => $packageC->id,
            'enrollment_date' => Carbon::parse('2026-01-10'),
            'total_fee' => $packageC->total_fee,
            'downpayment_percent' => $packageC->downpayment_percent,
            'downpayment_amount' => ($packageC->total_fee * $packageC->downpayment_percent) / 100,
            'remaining_balance' => $packageC->total_fee - (($packageC->total_fee * $packageC->downpayment_percent) / 100),
            'status' => 'ACTIVE',
        ]);

        // Generate payment schedules
        $paymentScheduleService = app(PaymentScheduleService::class);
        $paymentScheduleService->generateSchedules($enrollment);

        // Display results
        $this->command->info('=== Enrollment Test Case ===');
        $this->command->info("Student: {$student->full_name} ({$student->student_no})");
        $this->command->info("Package: {$packageC->name}");
        $this->command->info("Enrollment Date: {$enrollment->enrollment_date->format('Y-m-d')}");
        $this->command->info("Total Fee: ₱" . number_format($enrollment->total_fee, 2));
        $this->command->info("Downpayment ({$enrollment->downpayment_percent}%): ₱" . number_format($enrollment->downpayment_amount, 2));
        $this->command->info("Remaining Balance: ₱" . number_format($enrollment->remaining_balance, 2));
        $this->command->info('');
        $this->command->info('=== Payment Schedule ===');

        foreach ($enrollment->paymentSchedules()->orderBy('installment_no')->get() as $schedule) {
            $label = $schedule->installment_no == 0 ? 'Downpayment' : "Installment #{$schedule->installment_no}";
            $dueDate = $schedule->due_date ? $schedule->due_date->format('Y-m-d (M d)') : 'N/A';
            $this->command->info("{$label}: ₱" . number_format($schedule->amount_due, 2) . " - Due: {$dueDate}");
        }

        // Verify totals
        $totalScheduled = $enrollment->paymentSchedules()->sum('amount_due');
        $this->command->info('');
        $this->command->info("Total Scheduled: ₱" . number_format($totalScheduled, 2));
        $this->command->info("Expected Total: ₱" . number_format($enrollment->total_fee, 2));
        $this->command->info("Match: " . ($totalScheduled == $enrollment->total_fee ? '✓ YES' : '✗ NO'));
    }
}

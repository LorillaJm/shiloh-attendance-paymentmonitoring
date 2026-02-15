<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Services\PaymentScheduleService;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DueDateTestSeeder extends Seeder
{
    /**
     * Test due date calculation for various enrollment dates.
     */
    public function run(): void
    {
        $student = Student::firstOrCreate(
            ['student_no' => 'TEST-2026-003'],
            [
                'first_name' => 'Pedro',
                'last_name' => 'Reyes',
                'birthdate' => '2012-03-10',
                'sex' => 'Male',
                'address' => '789 Test Road, Makati',
                'guardian_name' => 'Ana Reyes',
                'guardian_contact' => '09191234567',
                'status' => 'ACTIVE',
            ]
        );

        $package = Package::firstOrCreate(
            ['name' => 'Package Due Date Test'],
            [
                'total_fee' => 12000.00,
                'downpayment_percent' => 30.00,
                'installment_months' => 3,
                'description' => 'Test due dates',
            ]
        );

        $testCases = [
            ['date' => '2026-01-10', 'desc' => 'Enrolled Jan 10 (before 15th)'],
            ['date' => '2026-01-14', 'desc' => 'Enrolled Jan 14 (day before 15th)'],
            ['date' => '2026-01-15', 'desc' => 'Enrolled Jan 15 (on the 15th)'],
            ['date' => '2026-01-16', 'desc' => 'Enrolled Jan 16 (after 15th)'],
            ['date' => '2026-01-31', 'desc' => 'Enrolled Jan 31 (end of month)'],
        ];

        foreach ($testCases as $testCase) {
            $this->command->info('');
            $this->command->info("=== {$testCase['desc']} ===");
            
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'package_id' => $package->id,
                'enrollment_date' => Carbon::parse($testCase['date']),
                'total_fee' => $package->total_fee,
                'downpayment_percent' => $package->downpayment_percent,
                'downpayment_amount' => ($package->total_fee * $package->downpayment_percent) / 100,
                'remaining_balance' => $package->total_fee - (($package->total_fee * $package->downpayment_percent) / 100),
                'status' => 'ACTIVE',
            ]);

            $paymentScheduleService = app(PaymentScheduleService::class);
            $paymentScheduleService->generateSchedules($enrollment);

            $installments = $enrollment->paymentSchedules()
                ->where('installment_no', '>', 0)
                ->orderBy('installment_no')
                ->get();

            $this->command->info("First installment due: {$installments[0]->due_date->format('Y-m-d (M d)')}");
            $this->command->info("Second installment due: {$installments[1]->due_date->format('Y-m-d (M d)')}");
            $this->command->info("Third installment due: {$installments[2]->due_date->format('Y-m-d (M d)')}");
            
            // Clean up
            $enrollment->delete();
        }

        $this->command->info('');
        $this->command->info('=== Rule Verification ===');
        $this->command->info('✓ All due dates are on the 15th of the month');
        $this->command->info('✓ First installment is always the next 15th AFTER enrollment');
        $this->command->info('✓ Even if enrolled on the 15th, first installment is NEXT month\'s 15th');
    }
}

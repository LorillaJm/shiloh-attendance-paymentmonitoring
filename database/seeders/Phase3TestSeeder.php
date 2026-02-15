<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use App\Services\PaymentScheduleService;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class Phase3TestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Phase 3 Test Data Seeder ===');
        $this->command->info('');

        // Create test students
        $students = [
            Student::firstOrCreate(['student_no' => 'TEST-2026-101'], [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'birthdate' => '2010-05-15',
                'sex' => 'Male',
                'address' => '123 Main St',
                'guardian_name' => 'Jane Smith',
                'guardian_contact' => '09171111111',
                'status' => 'ACTIVE',
            ]),
            Student::firstOrCreate(['student_no' => 'TEST-2026-102'], [
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'birthdate' => '2011-08-20',
                'sex' => 'Female',
                'address' => '456 Oak Ave',
                'guardian_name' => 'Pedro Garcia',
                'guardian_contact' => '09172222222',
                'status' => 'ACTIVE',
            ]),
            Student::firstOrCreate(['student_no' => 'TEST-2026-103'], [
                'first_name' => 'Carlos',
                'last_name' => 'Reyes',
                'birthdate' => '2012-03-10',
                'sex' => 'Male',
                'address' => '789 Pine Rd',
                'guardian_name' => 'Ana Reyes',
                'guardian_contact' => '09173333333',
                'status' => 'ACTIVE',
            ]),
        ];

        // Create test package
        $package = Package::firstOrCreate(['name' => 'Phase 3 Test Package'], [
            'total_fee' => 12000.00,
            'downpayment_percent' => 25.00,
            'installment_months' => 3,
            'description' => 'Test package for Phase 3',
        ]);

        $service = app(PaymentScheduleService::class);

        // Scenario 1: Student with paid downpayment and one installment
        $this->command->info('Creating Scenario 1: Partially paid enrollment...');
        $enrollment1 = Enrollment::create([
            'student_id' => $students[0]->id,
            'package_id' => $package->id,
            'enrollment_date' => Carbon::parse('2026-01-10'),
            'total_fee' => $package->total_fee,
            'downpayment_percent' => $package->downpayment_percent,
            'downpayment_amount' => ($package->total_fee * $package->downpayment_percent) / 100,
            'remaining_balance' => $package->total_fee - (($package->total_fee * $package->downpayment_percent) / 100),
            'status' => 'ACTIVE',
        ]);
        $service->generateSchedules($enrollment1);
        
        // Mark downpayment as paid
        $downpayment = $enrollment1->paymentSchedules()->where('installment_no', 0)->first();
        $downpayment->update([
            'status' => 'PAID',
            'paid_at' => Carbon::parse('2026-01-10 10:00:00'),
            'payment_method' => 'CASH',
            'receipt_no' => 'RCT-001',
        ]);

        // Mark first installment as paid today
        $firstInstallment = $enrollment1->paymentSchedules()->where('installment_no', 1)->first();
        $firstInstallment->update([
            'status' => 'PAID',
            'paid_at' => now(),
            'payment_method' => 'GCASH',
            'receipt_no' => 'RCT-002',
        ]);

        // Scenario 2: Student with overdue payment
        $this->command->info('Creating Scenario 2: Enrollment with overdue payment...');
        $enrollment2 = Enrollment::create([
            'student_id' => $students[1]->id,
            'package_id' => $package->id,
            'enrollment_date' => Carbon::parse('2025-12-05'), // Last year
            'total_fee' => $package->total_fee,
            'downpayment_percent' => $package->downpayment_percent,
            'downpayment_amount' => ($package->total_fee * $package->downpayment_percent) / 100,
            'remaining_balance' => $package->total_fee - (($package->total_fee * $package->downpayment_percent) / 100),
            'status' => 'ACTIVE',
        ]);
        $service->generateSchedules($enrollment2);
        
        // Mark downpayment as paid
        $downpayment2 = $enrollment2->paymentSchedules()->where('installment_no', 0)->first();
        $downpayment2->update([
            'status' => 'PAID',
            'paid_at' => Carbon::parse('2025-12-05 14:00:00'),
            'payment_method' => 'CASH',
            'receipt_no' => 'RCT-003',
        ]);
        // First installment is now overdue (due Jan 15, 2026)

        // Scenario 3: Student with payment due on next 15th
        $this->command->info('Creating Scenario 3: Enrollment with upcoming due date...');
        $today = now();
        $enrollmentDate = $today->copy()->subMonths(2)->day(10);
        
        $enrollment3 = Enrollment::create([
            'student_id' => $students[2]->id,
            'package_id' => $package->id,
            'enrollment_date' => $enrollmentDate,
            'total_fee' => $package->total_fee,
            'downpayment_percent' => $package->downpayment_percent,
            'downpayment_amount' => ($package->total_fee * $package->downpayment_percent) / 100,
            'remaining_balance' => $package->total_fee - (($package->total_fee * $package->downpayment_percent) / 100),
            'status' => 'ACTIVE',
        ]);
        $service->generateSchedules($enrollment3);
        
        // Mark downpayment and first installment as paid
        $downpayment3 = $enrollment3->paymentSchedules()->where('installment_no', 0)->first();
        $downpayment3->update([
            'status' => 'PAID',
            'paid_at' => $enrollmentDate,
            'payment_method' => 'BANK_TRANSFER',
            'receipt_no' => 'RCT-004',
        ]);

        $firstInst3 = $enrollment3->paymentSchedules()->where('installment_no', 1)->first();
        $firstInst3->update([
            'status' => 'PAID',
            'paid_at' => $firstInst3->due_date,
            'payment_method' => 'CASH',
            'receipt_no' => 'RCT-005',
        ]);

        $this->command->info('');
        $this->command->info('=== Test Data Summary ===');
        $this->command->info("Total Enrollments: 3");
        $this->command->info("Total Payments Made: 5");
        $this->command->info('');
        
        // Display balance information
        foreach ([$enrollment1, $enrollment2, $enrollment3] as $i => $enrollment) {
            $this->command->info("Enrollment " . ($i + 1) . ": {$enrollment->student->full_name}");
            $this->command->info("  Total Fee: ₱" . number_format($enrollment->total_fee, 2));
            $this->command->info("  Total Paid: ₱" . number_format($enrollment->total_paid, 2));
            $this->command->info("  Balance: ₱" . number_format($enrollment->remaining_balance_computed, 2));
            $this->command->info("  Status: {$enrollment->paid_count} paid, {$enrollment->unpaid_count} unpaid, {$enrollment->overdue_count} overdue");
            $this->command->info('');
        }

        // Display collections summary
        $paidToday = PaymentSchedule::where('status', 'PAID')
            ->whereDate('paid_at', now()->format('Y-m-d'))
            ->sum('amount_due');
        
        $paidThisMonth = PaymentSchedule::where('status', 'PAID')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount_due');

        $this->command->info('=== Collections Summary ===');
        $this->command->info("Paid Today: ₱" . number_format($paidToday, 2));
        $this->command->info("Paid This Month: ₱" . number_format($paidThisMonth, 2));
        $this->command->info('');

        // Display overdue count
        $overdueCount = PaymentSchedule::where('status', 'UNPAID')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->format('Y-m-d'))
            ->count();

        $this->command->info("Overdue Payments: {$overdueCount}");
        
        $this->command->info('');
        $this->command->info('✓ Phase 3 test data created successfully!');
    }
}

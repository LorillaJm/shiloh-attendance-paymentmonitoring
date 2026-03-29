<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Models\AttendanceRecord;
use App\Models\PaymentTransaction;
use Carbon\Carbon;

class ParentPortalDataSeeder extends Seeder
{
    /**
     * Seed sample data for parent portal testing.
     */
    public function run(): void
    {
        // Get test students
        $student1 = Student::where('student_no', 'TEST-001')->first();
        $student2 = Student::where('student_no', 'TEST-002')->first();

        if (!$student1 || !$student2) {
            $this->command->error('Test students not found. Run ParentPortalSeeder first.');
            return;
        }

        // Create or get a test package
        $package = Package::firstOrCreate(
            ['name' => 'Test Package'],
            [
                'description' => 'Test package for parent portal',
                'total_fee' => 10000.00,
                'downpayment_percent' => 30,
                'installment_months' => 3,
            ]
        );

        // Create enrollments for both students
        $this->createEnrollment($student1, $package);
        $this->createEnrollment($student2, $package);

        // Create attendance records
        $this->createAttendanceRecords($student1);
        $this->createAttendanceRecords($student2);

        $this->command->info('Parent portal sample data created successfully!');
    }

    private function createEnrollment($student, $package)
    {
        $enrollment = Enrollment::firstOrCreate(
            [
                'student_id' => $student->id,
                'package_id' => $package->id,
            ],
            [
                'enrollment_date' => now()->subMonths(1),
                'package_start_date' => now()->subMonths(1),
                'package_end_date' => now()->addMonths(2),
                'total_fee' => $package->total_fee,
                'downpayment_percent' => $package->downpayment_percent,
                'downpayment_amount' => $package->downpayment_amount,
                'remaining_balance' => $package->total_fee - $package->downpayment_amount,
                'monthly_installments' => $package->installment_months,
                'status' => 'ACTIVE',
            ]
        );

        // Get admin user for processed_by
        $adminUser = \App\Models\User::where('role', 'ADMIN')->first();
        if (!$adminUser) {
            $adminUser = \App\Models\User::first();
        }

        // Create payment transaction for downpayment
        PaymentTransaction::firstOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'transaction_date' => now()->subMonths(1),
            ],
            [
                'amount' => $enrollment->downpayment_amount,
                'type' => 'PAYMENT',
                'payment_method' => 'CASH',
                'reference_no' => 'DP-' . $student->student_no,
                'remarks' => 'Downpayment',
                'processed_by_user_id' => $adminUser->id,
            ]
        );

        // Create a recent payment
        PaymentTransaction::firstOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'transaction_date' => now()->subDays(5),
            ],
            [
                'amount' => 2000.00,
                'type' => 'PAYMENT',
                'payment_method' => 'GCASH',
                'reference_no' => 'PAY-' . $student->student_no . '-001',
                'remarks' => 'Monthly payment',
                'processed_by_user_id' => $adminUser->id,
            ]
        );
    }

    private function createAttendanceRecords($student)
    {
        $statuses = ['PRESENT', 'PRESENT', 'PRESENT', 'LATE', 'ABSENT'];
        
        // Get admin user for encoded_by
        $adminUser = \App\Models\User::where('role', 'ADMIN')->first();
        if (!$adminUser) {
            $adminUser = \App\Models\User::first();
        }
        
        // Create attendance for the last 10 days
        for ($i = 0; $i < 10; $i++) {
            $date = now()->subDays($i);
            
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            AttendanceRecord::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'attendance_date' => $date,
                ],
                [
                    'status' => $statuses[array_rand($statuses)],
                    'remarks' => $i === 0 ? 'Good behavior' : null,
                    'encoded_by_user_id' => $adminUser->id,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create packages first
        $packages = [
            [
                'name' => 'Basic Package',
                'description' => '10 sessions - Perfect for beginners',
                'total_fee' => 5000.00,
                'downpayment_percent' => 30.00,
                'installment_months' => 3,
            ],
            [
                'name' => 'Standard Package',
                'description' => '20 sessions - Most popular choice',
                'total_fee' => 8000.00,
                'downpayment_percent' => 40.00,
                'installment_months' => 3,
            ],
            [
                'name' => 'Premium Package',
                'description' => 'Unlimited sessions - Best value',
                'total_fee' => 12000.00,
                'downpayment_percent' => 50.00,
                'installment_months' => 3,
            ],
        ];

        foreach ($packages as $packageData) {
            Package::firstOrCreate(
                ['name' => $packageData['name']],
                $packageData
            );
        }

        // Create 10 students with realistic data
        $students = [
            ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'middle_name' => 'Santos', 'sex' => 'Male', 'birthdate' => '2015-03-15'],
            ['first_name' => 'Maria', 'last_name' => 'Garcia', 'middle_name' => 'Reyes', 'sex' => 'Female', 'birthdate' => '2016-07-22'],
            ['first_name' => 'Pedro', 'last_name' => 'Ramos', 'middle_name' => 'Cruz', 'sex' => 'Male', 'birthdate' => '2014-11-08'],
            ['first_name' => 'Ana', 'last_name' => 'Santos', 'middle_name' => 'Lopez', 'sex' => 'Female', 'birthdate' => '2015-05-30'],
            ['first_name' => 'Miguel', 'last_name' => 'Torres', 'middle_name' => 'Fernandez', 'sex' => 'Male', 'birthdate' => '2016-01-12'],
            ['first_name' => 'Sofia', 'last_name' => 'Mendoza', 'middle_name' => 'Rivera', 'sex' => 'Female', 'birthdate' => '2015-09-18'],
            ['first_name' => 'Carlos', 'last_name' => 'Villanueva', 'middle_name' => 'Gomez', 'sex' => 'Male', 'birthdate' => '2014-06-25'],
            ['first_name' => 'Isabella', 'last_name' => 'Aquino', 'middle_name' => 'Diaz', 'sex' => 'Female', 'birthdate' => '2016-04-03'],
            ['first_name' => 'Luis', 'last_name' => 'Bautista', 'middle_name' => 'Morales', 'sex' => 'Male', 'birthdate' => '2015-12-20'],
            ['first_name' => 'Elena', 'last_name' => 'Castillo', 'middle_name' => 'Navarro', 'sex' => 'Female', 'birthdate' => '2016-08-14'],
        ];

        $guardians = [
            ['name' => 'Roberto Dela Cruz', 'contact' => '+639171234567'],
            ['name' => 'Carmen Garcia', 'contact' => '+639182345678'],
            ['name' => 'Jose Ramos', 'contact' => '+639193456789'],
            ['name' => 'Teresa Santos', 'contact' => '+639204567890'],
            ['name' => 'Antonio Torres', 'contact' => '+639215678901'],
            ['name' => 'Rosa Mendoza', 'contact' => '+639226789012'],
            ['name' => 'Francisco Villanueva', 'contact' => '+639237890123'],
            ['name' => 'Luz Aquino', 'contact' => '+639248901234'],
            ['name' => 'Manuel Bautista', 'contact' => '+639259012345'],
            ['name' => 'Gloria Castillo', 'contact' => '+639260123456'],
        ];

        $addresses = [
            '123 Rizal Street, Quezon City, Metro Manila',
            '456 Bonifacio Avenue, Makati City, Metro Manila',
            '789 Aguinaldo Road, Pasig City, Metro Manila',
            '321 Luna Street, Manila City, Metro Manila',
            '654 Mabini Avenue, Mandaluyong City, Metro Manila',
            '987 Roxas Boulevard, Pasay City, Metro Manila',
            '147 Magsaysay Street, Taguig City, Metro Manila',
            '258 Quezon Avenue, Caloocan City, Metro Manila',
            '369 Recto Street, San Juan City, Metro Manila',
            '741 Escolta Street, Paranaque City, Metro Manila',
        ];

        $allPackages = Package::all();
        
        foreach ($students as $index => $studentData) {
            // Create student
            $student = Student::create([
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'middle_name' => $studentData['middle_name'],
                'sex' => $studentData['sex'],
                'birthdate' => $studentData['birthdate'],
                'address' => $addresses[$index],
                'guardian_name' => $guardians[$index]['name'],
                'guardian_contact' => $guardians[$index]['contact'],
                'status' => 'ACTIVE',
            ]);

            // Create enrollment
            $package = $allPackages->random();
            $enrollmentDate = Carbon::now()->subDays(rand(30, 90));
            
            $downpaymentAmount = $package->downpayment_amount;
            $remainingBalance = $package->total_fee - $downpaymentAmount;
            
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'package_id' => $package->id,
                'enrollment_date' => $enrollmentDate,
                'total_fee' => $package->total_fee,
                'downpayment_percent' => $package->downpayment_percent,
                'downpayment_amount' => $downpaymentAmount,
                'remaining_balance' => $remainingBalance,
                'status' => 'ACTIVE',
            ]);

            // Create payment schedules (3 installments)
            $installmentAmount = $remainingBalance / 3;
            $paymentStatuses = ['PAID', 'PAID', 'UNPAID']; // First 2 paid, last unpaid
            
            for ($i = 1; $i <= 3; $i++) {
                $dueDate = $enrollmentDate->copy()->addMonths($i)->day(15);
                $status = $paymentStatuses[$i - 1];
                
                $schedule = PaymentSchedule::create([
                    'enrollment_id' => $enrollment->id,
                    'installment_no' => $i,
                    'due_date' => $dueDate,
                    'amount_due' => $installmentAmount,
                    'status' => $status,
                    'paid_at' => $status === 'PAID' ? $dueDate->copy()->addDays(rand(-5, 5)) : null,
                    'payment_method' => $status === 'PAID' ? ['CASH', 'GCASH', 'BANK'][rand(0, 2)] : null,
                    'receipt_no' => $status === 'PAID' ? 'RCP-' . str_pad($enrollment->id * 10 + $i, 6, '0', STR_PAD_LEFT) : null,
                ]);
            }

            // Create attendance records for last 30 days
            $attendanceStatuses = ['PRESENT', 'PRESENT', 'PRESENT', 'PRESENT', 'LATE', 'ABSENT'];
            
            for ($day = 0; $day < 30; $day++) {
                $attendanceDate = Carbon::now()->subDays($day);
                
                // Skip weekends
                if ($attendanceDate->isWeekend()) {
                    continue;
                }
                
                AttendanceRecord::create([
                    'student_id' => $student->id,
                    'attendance_date' => $attendanceDate,
                    'status' => $attendanceStatuses[array_rand($attendanceStatuses)],
                    'remarks' => null,
                    'encoded_by_user_id' => 1, // Admin user
                ]);
            }
        }

        $this->command->info('✓ Created 10 students with enrollments, payments, and attendance');
        $this->command->info('✓ Students: ' . Student::count());
        $this->command->info('✓ Enrollments: ' . Enrollment::count());
        $this->command->info('✓ Payment Schedules: ' . PaymentSchedule::count());
        $this->command->info('✓ Attendance Records: ' . AttendanceRecord::count());
    }
}

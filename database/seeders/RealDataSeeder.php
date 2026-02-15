<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Package;
use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;

class RealDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create encoder user if not exists
        $encoder = User::firstOrCreate(
            ['email' => 'encoder@shiloh.local'],
            [
                'name' => 'Maria Santos',
                'password' => bcrypt('Encoder123!'),
                'role' => 'USER',
            ]
        );

        // Create packages/programs
        $packages = [
            [
                'name' => 'Basic Learning Program',
                'total_fee' => 15000.00,
                'downpayment_percent' => 30,
                'installment_months' => 3,
                'description' => 'Foundational learning program for beginners',
            ],
            [
                'name' => 'Advanced Learning Program',
                'total_fee' => 25000.00,
                'downpayment_percent' => 25,
                'installment_months' => 5,
                'description' => 'Comprehensive program with advanced modules',
            ],
            [
                'name' => 'Premium Learning Program',
                'total_fee' => 35000.00,
                'downpayment_percent' => 20,
                'installment_months' => 6,
                'description' => 'Full-featured premium program with all benefits',
            ],
        ];

        $createdPackages = [];
        foreach ($packages as $packageData) {
            $createdPackages[] = Package::firstOrCreate(
                ['name' => $packageData['name']],
                $packageData
            );
        }

        // Create 10 realistic students
        $students = [
            [
                'first_name' => 'Juan',
                'middle_name' => 'Santos',
                'last_name' => 'Dela Cruz',
                'birthdate' => '2015-03-15',
                'sex' => 'Male',
                'address' => '123 Rizal Street, Barangay San Jose, Manila',
                'guardian_name' => 'Maria Dela Cruz',
                'guardian_contact' => '09171234567',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Maria',
                'middle_name' => 'Garcia',
                'last_name' => 'Reyes',
                'birthdate' => '2016-07-22',
                'sex' => 'Female',
                'address' => '456 Bonifacio Avenue, Barangay Santa Cruz, Quezon City',
                'guardian_name' => 'Pedro Reyes',
                'guardian_contact' => '09181234567',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Jose',
                'middle_name' => 'Ramos',
                'last_name' => 'Santos',
                'birthdate' => '2014-11-08',
                'sex' => 'Male',
                'address' => '789 Mabini Street, Barangay Poblacion, Makati',
                'guardian_name' => 'Ana Santos',
                'guardian_contact' => '09191234567',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Ana',
                'middle_name' => 'Lopez',
                'last_name' => 'Cruz',
                'birthdate' => '2015-05-30',
                'sex' => 'Female',
                'address' => '321 Luna Street, Barangay San Miguel, Pasig',
                'guardian_name' => 'Roberto Cruz',
                'guardian_contact' => '09201234567',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Miguel',
                'middle_name' => 'Torres',
                'last_name' => 'Fernandez',
                'birthdate' => '2016-01-12',
                'sex' => 'Male',
                'address' => '654 Aguinaldo Road, Barangay Santo Niño, Taguig',
                'guardian_name' => 'Carmen Fernandez',
                'guardian_contact' => '09211234567',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Sofia',
                'middle_name' => 'Mendoza',
                'last_name' => 'Gonzales',
                'birthdate' => '2015-09-18',
                'sex' => 'Female',
                'address' => '987 Quezon Boulevard, Barangay San Antonio, Parañaque',
                'guardian_name' => 'Luis Gonzales',
                'guardian_contact' => '09221234567',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Gabriel',
                'middle_name' => 'Villanueva',
                'last_name' => 'Morales',
                'birthdate' => '2014-12-25',
                'sex' => 'Male',
                'address' => '147 Roxas Avenue, Barangay San Pedro, Las Piñas',
                'guardian_name' => 'Elena Morales',
                'guardian_contact' => '09231234567',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Isabella',
                'middle_name' => 'Castro',
                'last_name' => 'Rivera',
                'birthdate' => '2016-04-07',
                'sex' => 'Female',
                'address' => '258 Magsaysay Street, Barangay Santa Maria, Muntinlupa',
                'guardian_name' => 'Ricardo Rivera',
                'guardian_contact' => '09241234567',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Lucas',
                'middle_name' => 'Diaz',
                'last_name' => 'Aquino',
                'birthdate' => '2015-08-14',
                'sex' => 'Male',
                'address' => '369 Osmena Highway, Barangay San Isidro, Pasay',
                'guardian_name' => 'Teresa Aquino',
                'guardian_contact' => '09251234567',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Mia',
                'middle_name' => 'Flores',
                'last_name' => 'Bautista',
                'birthdate' => '2016-02-28',
                'sex' => 'Female',
                'address' => '741 Taft Avenue, Barangay San Rafael, Mandaluyong',
                'guardian_name' => 'Fernando Bautista',
                'guardian_contact' => '09261234567',
                'status' => 'ACTIVE',
            ],
        ];

        $createdStudents = [];
        foreach ($students as $studentData) {
            $createdStudents[] = Student::create($studentData);
        }

        // Create enrollments with varied scenarios
        $today = Carbon::now('Asia/Manila');
        $enrollmentScenarios = [
            // Fully paid students
            ['student_index' => 0, 'package_index' => 0, 'months_ago' => 6, 'fully_paid' => true],
            ['student_index' => 1, 'package_index' => 1, 'months_ago' => 5, 'fully_paid' => true],
            
            // Students with balance (on track)
            ['student_index' => 2, 'package_index' => 0, 'months_ago' => 3, 'fully_paid' => false],
            ['student_index' => 3, 'package_index' => 1, 'months_ago' => 4, 'fully_paid' => false],
            ['student_index' => 4, 'package_index' => 2, 'months_ago' => 2, 'fully_paid' => false],
            
            // Students with overdue payments
            ['student_index' => 5, 'package_index' => 0, 'months_ago' => 5, 'fully_paid' => false, 'skip_payments' => 2],
            ['student_index' => 6, 'package_index' => 1, 'months_ago' => 6, 'fully_paid' => false, 'skip_payments' => 1],
            
            // Recent enrollments
            ['student_index' => 7, 'package_index' => 0, 'months_ago' => 1, 'fully_paid' => false],
            ['student_index' => 8, 'package_index' => 1, 'months_ago' => 1, 'fully_paid' => false],
            ['student_index' => 9, 'package_index' => 2, 'months_ago' => 0, 'fully_paid' => false],
        ];

        foreach ($enrollmentScenarios as $scenario) {
            $student = $createdStudents[$scenario['student_index']];
            $package = $createdPackages[$scenario['package_index']];
            
            $enrollmentDate = $today->copy()->subMonths($scenario['months_ago']);
            
            $downpaymentAmount = ($package->total_fee * $package->downpayment_percent) / 100;
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

            // Create payment schedules
            $monthlyAmount = $package->installment_months > 0 
                ? $remainingBalance / $package->installment_months 
                : 0;

            // Downpayment
            $downpaymentDueDate = $enrollmentDate->copy();
            PaymentSchedule::create([
                'enrollment_id' => $enrollment->id,
                'installment_no' => 0,
                'due_date' => $downpaymentDueDate,
                'amount_due' => $downpaymentAmount,
                'status' => 'PAID',
                'paid_at' => $enrollmentDate->copy()->addDays(2),
                'payment_method' => 'CASH',
                'receipt_no' => 'RCP-' . str_pad($enrollment->id * 10, 6, '0', STR_PAD_LEFT),
            ]);

            // Monthly installments
            $skipPayments = $scenario['skip_payments'] ?? 0;
            for ($i = 1; $i <= $package->installment_months; $i++) {
                $dueDate = $enrollmentDate->copy()->addMonths($i)->day(15);
                $isPaid = $scenario['fully_paid'] || ($i <= ($scenario['months_ago'] - $skipPayments));
                
                $schedule = [
                    'enrollment_id' => $enrollment->id,
                    'installment_no' => $i,
                    'due_date' => $dueDate,
                    'amount_due' => $monthlyAmount,
                    'status' => $isPaid ? 'PAID' : 'UNPAID',
                ];

                if ($isPaid) {
                    $schedule['paid_at'] = $dueDate->copy()->addDays(rand(1, 5));
                    $schedule['payment_method'] = ['CASH', 'GCASH', 'BANK_TRANSFER'][rand(0, 2)];
                    $schedule['receipt_no'] = 'RCP-' . str_pad(($enrollment->id * 10) + $i, 6, '0', STR_PAD_LEFT);
                }

                PaymentSchedule::create($schedule);
            }

            // Update enrollment remaining balance
            $paidAmount = PaymentSchedule::where('enrollment_id', $enrollment->id)
                ->where('status', 'PAID')
                ->sum('amount_due');
            
            $enrollment->update([
                'remaining_balance' => $package->total_fee - $paidAmount,
            ]);
        }

        // Create attendance records for last 30 days
        $attendanceStatuses = ['PRESENT', 'ABSENT', 'LATE', 'EXCUSED'];
        $attendanceWeights = [85, 5, 8, 2]; // 85% present, 5% absent, 8% late, 2% excused

        for ($day = 29; $day >= 0; $day--) {
            $date = $today->copy()->subDays($day);
            
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($createdStudents as $student) {
                // 90% chance of having attendance record
                if (rand(1, 100) <= 90) {
                    $status = $this->weightedRandom($attendanceStatuses, $attendanceWeights);
                    
                    AttendanceRecord::create([
                        'student_id' => $student->id,
                        'attendance_date' => $date,
                        'status' => $status,
                        'remarks' => $status === 'LATE' ? 'Arrived 15 minutes late' : 
                                   ($status === 'EXCUSED' ? 'Medical appointment' : null),
                        'encoded_by_user_id' => $encoder->id,
                        'created_at' => $date->copy()->setTime(8, 30),
                        'updated_at' => $date->copy()->setTime(8, 30),
                    ]);
                }
            }
        }

        $this->command->info('✅ Successfully seeded 10 students with realistic data!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - 10 Students created');
        $this->command->info('   - 3 Programs created');
        $this->command->info('   - 10 Registrations created');
        $this->command->info('   - ' . PaymentSchedule::count() . ' Payment schedules created');
        $this->command->info('   - ' . AttendanceRecord::count() . ' Attendance records created');
        $this->command->info('');
        $this->command->info('💡 Login credentials:');
        $this->command->info('   Admin: admin@shiloh.local / Admin123!');
        $this->command->info('   Encoder: encoder@shiloh.local / Encoder123!');
    }

    private function weightedRandom(array $values, array $weights): mixed
    {
        $totalWeight = array_sum($weights);
        $random = rand(1, $totalWeight);
        
        $currentWeight = 0;
        foreach ($values as $index => $value) {
            $currentWeight += $weights[$index];
            if ($random <= $currentWeight) {
                return $value;
            }
        }
        
        return $values[0];
    }
}

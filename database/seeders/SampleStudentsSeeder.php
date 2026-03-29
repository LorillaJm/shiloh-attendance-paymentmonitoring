<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Guardian;
use App\Models\User;
use App\Models\Package;
use App\Models\Enrollment;
use App\Models\PaymentSchedule;
use App\Models\PaymentTransaction;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleStudentsSeeder extends Seeder
{
    public function run(): void
    {
        // Create a package first if none exists
        $package = Package::firstOrCreate(
            ['name' => 'Basic Therapy Package'],
            [
                'total_fee' => 10000.00,
                'downpayment_percent' => 20.00,
                'installment_months' => 4,
                'description' => 'Basic therapy package with 4 monthly installments',
            ]
        );

        $students = [
            ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'sex' => 'Male', 'age' => 8],
            ['first_name' => 'Maria', 'last_name' => 'Santos', 'sex' => 'Female', 'age' => 10],
            ['first_name' => 'Pedro', 'last_name' => 'Reyes', 'sex' => 'Male', 'age' => 12],
            ['first_name' => 'Ana', 'last_name' => 'Garcia', 'sex' => 'Female', 'age' => 7],
            ['first_name' => 'Jose', 'last_name' => 'Ramos', 'sex' => 'Male', 'age' => 9],
        ];

        foreach ($students as $index => $studentData) {
            $birthdate = now()->subYears($studentData['age']);
            
            // Create student
            $student = Student::create([
                'first_name' => $studentData['first_name'],
                'last_name' => $studentData['last_name'],
                'middle_name' => 'M.',
                'birthdate' => $birthdate,
                'sex' => $studentData['sex'],
                'address' => 'Sample Address ' . ($index + 1) . ', Manila',
                'guardian_name' => 'Parent of ' . $studentData['first_name'],
                'guardian_contact' => '09' . str_pad($index + 1, 9, '0', STR_PAD_LEFT),
                'status' => 'ACTIVE',
            ]);

            // Create guardian user
            $guardianUser = User::create([
                'name' => 'Parent of ' . $studentData['first_name'] . ' ' . $studentData['last_name'],
                'email' => 'parent' . ($index + 1) . '@example.com',
                'password' => Hash::make('password'),
                'role' => UserRole::PARENT,
            ]);

            // Create guardian profile
            $guardian = Guardian::create([
                'user_id' => $guardianUser->id,
                'first_name' => 'Parent',
                'last_name' => $studentData['last_name'],
                'middle_name' => 'G.',
                'contact_number' => '09' . str_pad($index + 1, 9, '0', STR_PAD_LEFT),
                'email' => $guardianUser->email,
                'address' => $student->address,
                'relationship' => $index % 2 == 0 ? 'Father' : 'Mother',
            ]);

            // Link guardian to student
            $guardian->students()->attach($student->id, ['is_primary' => true]);

            // Create enrollment
            $downpaymentAmount = ($package->total_fee * $package->downpayment_percent) / 100;
            $remainingBalance = $package->total_fee - $downpaymentAmount;
            
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'package_id' => $package->id,
                'enrollment_date' => now()->subDays(30),
                'total_fee' => $package->total_fee,
                'downpayment_percent' => $package->downpayment_percent,
                'downpayment_amount' => $downpaymentAmount,
                'remaining_balance' => $remainingBalance,
                'status' => 'ACTIVE',
            ]);

            // Create downpayment transaction
            PaymentTransaction::create([
                'enrollment_id' => $enrollment->id,
                'student_id' => $student->id,
                'type' => 'PAYMENT',
                'amount' => $downpaymentAmount,
                'payment_date' => now()->subDays(30),
                'payment_method' => 'CASH',
                'reference_number' => 'DP-' . str_pad($student->id, 6, '0', STR_PAD_LEFT),
                'notes' => 'Downpayment',
            ]);

            // Create payment schedules (4 monthly installments)
            $monthlyAmount = $remainingBalance / $package->installment_months;
            
            for ($month = 1; $month <= $package->installment_months; $month++) {
                $dueDate = now()->subDays(30)->addMonths($month);
                $isPaid = $month <= 2; // First 2 installments are paid
                
                $schedule = PaymentSchedule::create([
                    'enrollment_id' => $enrollment->id,
                    'student_id' => $student->id,
                    'due_date' => $dueDate,
                    'amount_due' => $monthlyAmount,
                    'amount_paid' => $isPaid ? $monthlyAmount : 0,
                    'status' => $isPaid ? 'PAID' : ($dueDate->isPast() ? 'OVERDUE' : 'UNPAID'),
                    'paid_at' => $isPaid ? $dueDate : null,
                    'payment_method' => $isPaid ? 'CASH' : null,
                ]);

                // Create payment transaction for paid schedules
                if ($isPaid) {
                    PaymentTransaction::create([
                        'enrollment_id' => $enrollment->id,
                        'student_id' => $student->id,
                        'payment_schedule_id' => $schedule->id,
                        'type' => 'PAYMENT',
                        'amount' => $monthlyAmount,
                        'payment_date' => $dueDate,
                        'payment_method' => 'CASH',
                        'reference_number' => 'INS-' . str_pad($schedule->id, 6, '0', STR_PAD_LEFT),
                        'notes' => 'Installment ' . $month,
                    ]);
                }
            }
        }

        $this->command->info('Created 5 students with parents and payment records!');
        $this->command->info('Parent logins: parent1@example.com to parent5@example.com');
        $this->command->info('Password: password');
    }
}

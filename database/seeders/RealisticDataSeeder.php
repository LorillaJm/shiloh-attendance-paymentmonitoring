<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Package;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\AttendanceRecord;
use App\Services\PaymentScheduleService;
use Carbon\Carbon;

class RealisticDataSeeder extends Seeder
{
    private PaymentScheduleService $paymentService;

    public function __construct()
    {
        $this->paymentService = new PaymentScheduleService();
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting realistic data seeding...');

        // Create users for encoding attendance
        $this->command->info('Creating users...');
        $users = $this->createUsers();

        // Create 3 packages
        $this->command->info('Creating packages...');
        $packages = $this->createPackages();

        // Create 120 students
        $this->command->info('Creating 120 students...');
        $students = $this->createStudents(120);

        // Create 120 enrollments with distributed dates
        $this->command->info('Creating 120 enrollments...');
        $enrollments = $this->createEnrollments($students, $packages);

        // Generate payment schedules and process payments
        $this->command->info('Processing payments (30% fully paid, 30% partially paid, 40% unpaid)...');
        $this->processPayments($enrollments);

        // Create attendance records for last 30 days
        $this->command->info('Creating attendance records for last 30 days...');
        $this->createAttendanceRecords($students, $users);

        $this->command->info('✅ Realistic data seeding completed!');
        $this->printSummary($packages, $students, $enrollments);
    }

    /**
     * Create users for encoding attendance
     */
    private function createUsers(): array
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@shiloh.local'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('Admin123!'),
                'role' => 'ADMIN',
            ]
        );

        $user1 = User::firstOrCreate(
            ['email' => 'encoder1@shiloh.local'],
            [
                'name' => 'Encoder One',
                'password' => bcrypt('password'),
                'role' => 'USER',
            ]
        );

        $user2 = User::firstOrCreate(
            ['email' => 'encoder2@shiloh.local'],
            [
                'name' => 'Encoder Two',
                'password' => bcrypt('password'),
                'role' => 'USER',
            ]
        );

        return [$admin, $user1, $user2];
    }

    /**
     * Create 3 packages with different configurations
     */
    private function createPackages(): array
    {
        $packages = [
            [
                'name' => 'Basic Training Package',
                'total_fee' => 8000.00,
                'downpayment_percent' => 25.00,
                'installment_months' => 3,
                'description' => 'Entry-level training program with 3-month payment plan',
            ],
            [
                'name' => 'Standard Training Package',
                'total_fee' => 12000.00,
                'downpayment_percent' => 20.00,
                'installment_months' => 4,
                'description' => 'Comprehensive training with 4-month payment plan',
            ],
            [
                'name' => 'Premium Training Package',
                'total_fee' => 18000.00,
                'downpayment_percent' => 30.00,
                'installment_months' => 6,
                'description' => 'Advanced training program with 6-month payment plan',
            ],
        ];

        $created = [];
        foreach ($packages as $packageData) {
            $created[] = Package::firstOrCreate(
                ['name' => $packageData['name']],
                $packageData
            );
        }

        return $created;
    }

    /**
     * Create students
     */
    private function createStudents(int $count): array
    {
        $students = [];
        
        for ($i = 0; $i < $count; $i++) {
            $students[] = Student::create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'middle_name' => fake()->optional(0.7)->lastName(),
                'birthdate' => fake()->dateTimeBetween('-18 years', '-5 years'),
                'sex' => fake()->randomElement(['Male', 'Female']),
                'address' => fake()->address(),
                'guardian_name' => fake()->name(),
                'guardian_contact' => '+639' . fake()->numerify('#########'),
                'status' => 'ACTIVE',
            ]);
        }

        return $students;
    }

    /**
     * Create enrollments distributed across different dates
     */
    private function createEnrollments(array $students, array $packages): array
    {
        $enrollments = [];
        
        // Enrollment dates distributed over last 6 months
        $enrollmentDates = $this->generateEnrollmentDates(120);

        foreach ($students as $index => $student) {
            $package = $packages[array_rand($packages)];
            $enrollmentDate = $enrollmentDates[$index];

            $downpaymentAmount = round(($package->total_fee * $package->downpayment_percent) / 100, 2);
            $remainingBalance = round($package->total_fee - $downpaymentAmount, 2);

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

            // Generate payment schedules
            $this->paymentService->generateSchedules($enrollment);

            $enrollments[] = $enrollment;
        }

        return $enrollments;
    }

    /**
     * Generate distributed enrollment dates
     */
    private function generateEnrollmentDates(int $count): array
    {
        $dates = [];
        $startDate = Carbon::now()->subMonths(6);
        $endDate = Carbon::now();

        // Specific test dates
        $testDates = [
            '2026-01-01',  // Jan 1
            '2026-01-10',  // Jan 10
            '2026-01-15',  // Jan 15
            '2026-01-16',  // Jan 16
            '2026-01-31',  // Jan 31
            '2024-02-29',  // Leap year Feb
        ];

        // Add test dates first
        foreach ($testDates as $date) {
            if (count($dates) < $count) {
                $dates[] = $date;
            }
        }

        // Fill remaining with random dates
        while (count($dates) < $count) {
            $randomDate = Carbon::instance(fake()->dateTimeBetween($startDate, $endDate));
            $dates[] = $randomDate->format('Y-m-d');
        }

        shuffle($dates);
        return $dates;
    }

    /**
     * Process payments: 30% fully paid, 30% partially paid, 40% unpaid
     */
    private function processPayments(array $enrollments): void
    {
        $totalCount = count($enrollments);
        $fullyPaidCount = (int) ($totalCount * 0.30);
        $partiallyPaidCount = (int) ($totalCount * 0.30);

        shuffle($enrollments);

        // 30% fully paid
        for ($i = 0; $i < $fullyPaidCount; $i++) {
            $this->markAllSchedulesPaid($enrollments[$i]);
        }

        // 30% partially paid
        for ($i = $fullyPaidCount; $i < $fullyPaidCount + $partiallyPaidCount; $i++) {
            $this->markSomeSchedulesPaid($enrollments[$i]);
        }

        // 40% unpaid (no action needed)
    }

    /**
     * Mark all payment schedules as paid
     */
    private function markAllSchedulesPaid(Enrollment $enrollment): void
    {
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();

        foreach ($schedules as $schedule) {
            $paymentMethod = fake()->randomElement(['CASH', 'BANK_TRANSFER', 'GCASH']);
            $receiptNo = 'REC-' . fake()->unique()->numerify('######');
            
            $this->paymentService->markAsPaid(
                $schedule,
                $paymentMethod,
                $receiptNo,
                'Paid in full'
            );
        }
    }

    /**
     * Mark some payment schedules as paid (random 30-70%)
     */
    private function markSomeSchedulesPaid(Enrollment $enrollment): void
    {
        $schedules = $enrollment->paymentSchedules()->orderBy('installment_no')->get();
        $totalSchedules = $schedules->count();
        
        // Pay between 30% to 70% of schedules
        $payCount = rand((int)($totalSchedules * 0.3), (int)($totalSchedules * 0.7));
        $payCount = max(1, $payCount); // At least 1 payment

        for ($i = 0; $i < $payCount; $i++) {
            $schedule = $schedules[$i];
            $paymentMethod = fake()->randomElement(['CASH', 'BANK_TRANSFER', 'GCASH']);
            $receiptNo = 'REC-' . fake()->unique()->numerify('######');
            
            $this->paymentService->markAsPaid(
                $schedule,
                $paymentMethod,
                $receiptNo
            );
        }
    }

    /**
     * Create attendance records for last 30 days
     */
    private function createAttendanceRecords(array $students, array $users): void
    {
        $startDate = Carbon::now()->subDays(29);
        $endDate = Carbon::now();

        // For each day in the last 30 days
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            // Skip weekends (optional - remove if training is 7 days/week)
            if ($date->isWeekend()) {
                continue;
            }

            // Randomly select 80-100% of students to have attendance
            $attendingStudents = fake()->randomElements(
                $students,
                rand((int)(count($students) * 0.8), count($students))
            );

            $encoder = $users[array_rand($users)];

            foreach ($attendingStudents as $student) {
                // 85% present, 10% absent, 5% late
                $rand = rand(1, 100);
                if ($rand <= 85) {
                    $status = 'PRESENT';
                    $remarks = null;
                } elseif ($rand <= 95) {
                    $status = 'ABSENT';
                    $remarks = fake()->optional(0.5)->randomElement([
                        'Sick',
                        'Family emergency',
                        'No call no show',
                    ]);
                } else {
                    $status = 'LATE';
                    $remarks = 'Arrived ' . rand(10, 60) . ' minutes late';
                }

                AttendanceRecord::create([
                    'student_id' => $student->id,
                    'attendance_date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'remarks' => $remarks,
                    'encoded_by_user_id' => $encoder->id,
                ]);
            }
        }
    }

    /**
     * Print summary of seeded data
     */
    private function printSummary(array $packages, array $students, array $enrollments): void
    {
        $this->command->info('');
        $this->command->info('📊 Seeding Summary:');
        $this->command->info('-------------------');
        $this->command->info('Packages: ' . count($packages));
        $this->command->info('Students: ' . count($students));
        $this->command->info('Enrollments: ' . count($enrollments));
        
        $totalSchedules = 0;
        $paidSchedules = 0;
        foreach ($enrollments as $enrollment) {
            $schedules = $enrollment->paymentSchedules;
            $totalSchedules += $schedules->count();
            $paidSchedules += $schedules->where('status', 'PAID')->count();
        }
        
        $this->command->info('Payment Schedules: ' . $totalSchedules);
        $this->command->info('Paid Schedules: ' . $paidSchedules);
        $this->command->info('Unpaid Schedules: ' . ($totalSchedules - $paidSchedules));
        
        $attendanceCount = AttendanceRecord::count();
        $this->command->info('Attendance Records: ' . $attendanceCount);
        $this->command->info('');
    }
}

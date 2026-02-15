<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class Phase4TestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Phase 4 Test Data Seeder ===');
        $this->command->info('');

        // Create test users (encoder and admin)
        $encoder = User::firstOrCreate(
            ['email' => 'encoder@shiloh.local'],
            [
                'name' => 'Attendance Encoder',
                'password' => \Hash::make('password'),
                'role' => 'USER',
            ]
        );

        $admin = User::where('email', 'admin@admin.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => \Hash::make('admin'),
                'role' => 'ADMIN',
            ]);
        }

        $this->command->info('Users created:');
        $this->command->info("  - Encoder: encoder@shiloh.local / password");
        $this->command->info("  - Admin: admin@admin.com / admin");
        $this->command->info('');

        // Get or create test students
        $students = [];
        for ($i = 1; $i <= 10; $i++) {
            $students[] = Student::firstOrCreate(
                ['student_no' => sprintf('ATT-2026-%03d', $i)],
                [
                    'first_name' => 'Student',
                    'last_name' => 'Number ' . $i,
                    'birthdate' => Carbon::now()->subYears(10)->subDays($i),
                    'sex' => $i % 2 == 0 ? 'Male' : 'Female',
                    'address' => $i . ' Test Street',
                    'guardian_name' => 'Guardian ' . $i,
                    'guardian_contact' => '0917' . str_pad($i, 7, '0', STR_PAD_LEFT),
                    'status' => 'ACTIVE',
                ]
            );
        }

        $this->command->info('Created 10 test students (ATT-2026-001 to ATT-2026-010)');
        $this->command->info('');

        // Create attendance records for the past 7 days
        $statuses = ['PRESENT', 'ABSENT', 'LATE', 'EXCUSED'];
        $recordsCreated = 0;

        for ($day = 6; $day >= 0; $day--) {
            $date = Carbon::now()->subDays($day);
            
            foreach ($students as $index => $student) {
                // Most students present, some variations
                if ($day == 0) {
                    // Today - mix of statuses
                    $status = $statuses[$index % 4];
                } elseif ($day == 1) {
                    // Yesterday - mostly present
                    $status = $index % 5 == 0 ? 'ABSENT' : 'PRESENT';
                } else {
                    // Older days - mostly present with some late
                    $status = $index % 7 == 0 ? 'LATE' : 'PRESENT';
                }

                $remarks = null;
                if ($status === 'ABSENT') {
                    $remarks = 'Sick';
                } elseif ($status === 'EXCUSED') {
                    $remarks = 'Family emergency';
                } elseif ($status === 'LATE') {
                    $remarks = 'Traffic';
                }

                AttendanceRecord::create([
                    'student_id' => $student->id,
                    'attendance_date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'remarks' => $remarks,
                    'encoded_by_user_id' => $day % 2 == 0 ? $encoder->id : $admin->id,
                ]);

                $recordsCreated++;
            }
        }

        $this->command->info("Created {$recordsCreated} attendance records for the past 7 days");
        $this->command->info('');

        // Display summary
        $this->command->info('=== Attendance Summary ===');
        
        $today = Carbon::now()->format('Y-m-d');
        $todayRecords = AttendanceRecord::whereDate('attendance_date', $today)->get();
        
        $this->command->info("Today ({$today}):");
        $this->command->info("  Present: " . $todayRecords->where('status', 'PRESENT')->count());
        $this->command->info("  Absent: " . $todayRecords->where('status', 'ABSENT')->count());
        $this->command->info("  Late: " . $todayRecords->where('status', 'LATE')->count());
        $this->command->info("  Excused: " . $todayRecords->where('status', 'EXCUSED')->count());
        $this->command->info('');

        $thisMonth = AttendanceRecord::whereYear('attendance_date', now()->year)
            ->whereMonth('attendance_date', now()->month)
            ->get();

        $this->command->info("This Month:");
        $this->command->info("  Total Records: " . $thisMonth->count());
        $this->command->info("  Present: " . $thisMonth->where('status', 'PRESENT')->count());
        $this->command->info("  Absent: " . $thisMonth->where('status', 'ABSENT')->count());
        $this->command->info("  Late: " . $thisMonth->where('status', 'LATE')->count());
        $this->command->info("  Excused: " . $thisMonth->where('status', 'EXCUSED')->count());
        $this->command->info('');

        $this->command->info('✓ Phase 4 test data created successfully!');
        $this->command->info('');
        $this->command->info('You can now:');
        $this->command->info('  - Login as encoder@shiloh.local / password (USER role)');
        $this->command->info('  - Login as admin@admin.com / admin (ADMIN role)');
        $this->command->info('  - Use Daily Attendance Encoder');
        $this->command->info('  - View Daily Attendance Report');
        $this->command->info('  - View Monthly Attendance Summary');
    }
}

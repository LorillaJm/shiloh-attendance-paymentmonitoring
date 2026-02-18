<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\SessionType;
use App\Models\StudentSchedule;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class SampleSchedulesSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::active()->limit(10)->get();
        $sessionTypes = SessionType::all();
        $teachers = User::where('role', UserRole::TEACHER)->get();

        if ($teachers->isEmpty() || $sessionTypes->isEmpty()) {
            $this->command->warn('Please seed teachers and session types first.');
            return;
        }

        foreach ($students as $index => $student) {
            // Assign 1-2 sessions per student
            $numSessions = rand(1, 2);
            
            for ($i = 0; $i < $numSessions; $i++) {
                $sessionType = $sessionTypes->random();
                $teacher = $teachers->random();
                
                // Vary the schedule patterns
                $patterns = [
                    ['type' => 'WEEKLY', 'days' => [1, 3, 5]], // Mon, Wed, Fri
                    ['type' => 'WEEKLY', 'days' => [2, 4]], // Tue, Thu
                    ['type' => 'WEEKLY', 'days' => [1, 4]], // Mon, Thu
                    ['type' => 'DAILY', 'days' => null],
                ];
                
                $pattern = $patterns[array_rand($patterns)];
                
                StudentSchedule::create([
                    'student_id' => $student->id,
                    'session_type_id' => $sessionType->id,
                    'teacher_id' => $teacher->id,
                    'recurrence_type' => $pattern['type'],
                    'recurrence_days' => $pattern['days'],
                    'start_time' => sprintf('%02d:00', 9 + ($i * 2)),
                    'end_time' => sprintf('%02d:00', 10 + ($i * 2)),
                    'effective_from' => now(),
                    'effective_until' => now()->addMonths(3),
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Sample schedules created for 10 students.');
    }
}

<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\SessionType;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'session_type_id' => SessionType::factory(),
            'teacher_id' => User::factory()->create(['role' => UserRole::TEACHER]),
            'recurrence_type' => fake()->randomElement(['DAILY', 'WEEKLY']),
            'recurrence_days' => [1, 3, 5], // Mon, Wed, Fri
            'start_time' => '09:00',
            'end_time' => '10:00',
            'effective_from' => now(),
            'effective_until' => now()->addMonths(3),
            'is_active' => true,
        ];
    }
}

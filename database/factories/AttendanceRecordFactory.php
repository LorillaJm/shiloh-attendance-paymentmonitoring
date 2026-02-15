<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'attendance_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'status' => fake()->randomElement(['PRESENT', 'ABSENT', 'LATE', 'EXCUSED']),
            'remarks' => fake()->optional(0.3)->sentence(),
            'encoded_by_user_id' => User::factory(),
        ];
    }

    public function present(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'PRESENT',
        ]);
    }

    public function absent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ABSENT',
        ]);
    }
}

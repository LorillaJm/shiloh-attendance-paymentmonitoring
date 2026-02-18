<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $birthdate = fake()->dateTimeBetween('-15 years', '-3 years');

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_name' => fake()->optional(0.7)->lastName(),
            'birthdate' => $birthdate,
            'sex' => fake()->randomElement(['Male', 'Female']),
            'address' => fake()->address(),
            'guardian_name' => fake()->name(),
            'guardian_contact' => '+639' . fake()->numerify('#########'),
            'status' => 'ACTIVE',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'INACTIVE',
        ]);
    }

    public function dropped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'DROPPED',
        ]);
    }
}

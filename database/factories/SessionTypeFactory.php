<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SessionTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'code' => strtoupper(fake()->unique()->word()),
            'description' => fake()->sentence(),
            'default_duration_minutes' => fake()->randomElement([30, 45, 60, 90]),
            'requires_monitoring' => fake()->boolean(),
            'is_active' => true,
        ];
    }
}

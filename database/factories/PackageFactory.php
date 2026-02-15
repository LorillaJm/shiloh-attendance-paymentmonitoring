<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true) . ' Package',
            'total_fee' => fake()->randomElement([5000, 8000, 10000, 12000, 15000, 20000]),
            'downpayment_percent' => fake()->randomElement([10, 20, 25, 30, 50]),
            'installment_months' => fake()->randomElement([3, 4, 6, 12]),
            'description' => fake()->optional()->sentence(),
        ];
    }
}

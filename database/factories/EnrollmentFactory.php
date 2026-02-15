<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        $package = Package::inRandomOrder()->first() ?? Package::factory()->create();
        
        $totalFee = $package->total_fee;
        $downpaymentPercent = $package->downpayment_percent;
        $downpaymentAmount = round(($totalFee * $downpaymentPercent) / 100, 2);
        $remainingBalance = round($totalFee - $downpaymentAmount, 2);

        return [
            'student_id' => Student::factory(),
            'package_id' => $package->id,
            'enrollment_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'total_fee' => $totalFee,
            'downpayment_percent' => $downpaymentPercent,
            'downpayment_amount' => $downpaymentAmount,
            'remaining_balance' => $remainingBalance,
            'status' => 'ACTIVE',
        ];
    }
}

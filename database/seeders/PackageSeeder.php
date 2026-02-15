<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Package A',
                'total_fee' => 15000.00,
                'downpayment_percent' => 25.00,
                'installment_months' => 3,
                'description' => 'Basic package with essential learning materials and 3-month access to online resources.',
            ],
            [
                'name' => 'Package B',
                'total_fee' => 25000.00,
                'downpayment_percent' => 30.00,
                'installment_months' => 4,
                'description' => 'Standard package including all basic features plus additional workshops and mentoring sessions.',
            ],
            [
                'name' => 'Package C',
                'total_fee' => 35000.00,
                'downpayment_percent' => 35.00,
                'installment_months' => 5,
                'description' => 'Premium package with comprehensive learning materials, unlimited online access, one-on-one mentoring, and certification.',
            ],
        ];

        foreach ($packages as $package) {
            Package::firstOrCreate(
                ['name' => $package['name']],
                $package
            );
        }

        $this->command->info('Sample packages created successfully!');
        $this->command->info('- Package A: ₱15,000 (25% down, 3 months)');
        $this->command->info('- Package B: ₱25,000 (30% down, 4 months)');
        $this->command->info('- Package C: ₱35,000 (35% down, 5 months)');
    }
}

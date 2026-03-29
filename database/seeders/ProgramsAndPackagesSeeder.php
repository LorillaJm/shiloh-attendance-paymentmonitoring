<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramsAndPackagesSeeder extends Seeder
{
    public function run(): void
    {
        // Create Programs/Services
        $programs = [
            [
                'name' => 'SPED Tutoring',
                'description' => 'Special Education tutoring services',
                'is_active' => true,
            ],
            [
                'name' => 'Behavior Support Program',
                'description' => 'Behavioral intervention and support services',
                'is_active' => true,
            ],
            [
                'name' => 'Speech & Communication Skills Program',
                'description' => 'Speech therapy and communication skills development',
                'is_active' => true,
            ],
            [
                'name' => 'Early Intervention',
                'description' => 'Early childhood intervention services',
                'is_active' => true,
            ],
            [
                'name' => 'Academic Tutoring',
                'description' => 'Academic support and tutoring services',
                'is_active' => true,
            ],
            [
                'name' => 'Reading and Writing Program',
                'description' => 'Literacy development and writing skills',
                'is_active' => true,
            ],
        ];

        foreach ($programs as $programData) {
            Program::firstOrCreate(
                ['name' => $programData['name']],
                $programData
            );
        }

        // Create Packages
        $packages = [
            [
                'name' => 'Package A: 1 Program',
                'total_fee' => 5000.00,
                'downpayment_percent' => 25.00,
                'installment_months' => 3,
                'description' => 'PHP 500.00 x 10 sessions = PHP 5,000. Choose up to 1 program.',
                'max_programs' => 1,
            ],
            [
                'name' => 'Package B: Up to 3 Programs',
                'total_fee' => 8000.00,
                'downpayment_percent' => 25.00,
                'installment_months' => 3,
                'description' => 'PHP 400 x 20 sessions = PHP 8,000. Choose up to 3 programs.',
                'max_programs' => 3,
            ],
            [
                'name' => 'Package C: Up to 5 Programs',
                'total_fee' => 15000.00,
                'downpayment_percent' => 25.00,
                'installment_months' => 3,
                'description' => 'PHP 300 x 50 sessions = PHP 15,000. Choose up to 5 programs.',
                'max_programs' => 5,
            ],
        ];

        foreach ($packages as $packageData) {
            Package::firstOrCreate(
                ['name' => $packageData['name']],
                [
                    'total_fee' => $packageData['total_fee'],
                    'downpayment_percent' => $packageData['downpayment_percent'],
                    'installment_months' => $packageData['installment_months'],
                    'description' => $packageData['description'],
                ]
            );
        }

        $this->command->info('Programs and Packages seeded successfully!');
    }
}

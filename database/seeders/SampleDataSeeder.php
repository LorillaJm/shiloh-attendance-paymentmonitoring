<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Student;
use App\Enums\StudentStatus;
use App\Enums\Sex;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create packages
        $packages = [
            [
                'name' => 'Basic Package',
                'description' => 'Basic learning package',
                'total_fee' => 5000.00,
                'downpayment_percent' => 20.00,
                'installment_months' => 3,
            ],
            [
                'name' => 'Standard Package',
                'description' => 'Standard learning package',
                'total_fee' => 12000.00,
                'downpayment_percent' => 25.00,
                'installment_months' => 6,
            ],
            [
                'name' => 'Premium Package',
                'description' => 'Premium learning package',
                'total_fee' => 20000.00,
                'downpayment_percent' => 30.00,
                'installment_months' => 12,
            ],
        ];

        foreach ($packages as $packageData) {
            Package::firstOrCreate(
                ['name' => $packageData['name']],
                $packageData
            );
        }

        // Create sample students
        $students = [
            [
                'student_no' => 'STU-2026-001',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'middle_name' => 'Smith',
                'birthdate' => '2015-05-15',
                'sex' => Sex::MALE,
                'status' => StudentStatus::ACTIVE,
                'guardian_name' => 'Robert Doe',
                'guardian_contact' => '09171234567',
                'address' => '123 Main St, Manila',
            ],
            [
                'student_no' => 'STU-2026-002',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'middle_name' => 'Marie',
                'birthdate' => '2016-08-20',
                'sex' => Sex::FEMALE,
                'status' => StudentStatus::ACTIVE,
                'guardian_name' => 'Mary Smith',
                'guardian_contact' => '09181234567',
                'address' => '456 Oak Ave, Quezon City',
            ],
            [
                'student_no' => 'STU-2026-003',
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'middle_name' => 'Lee',
                'birthdate' => '2014-03-10',
                'sex' => Sex::MALE,
                'status' => StudentStatus::ACTIVE,
                'guardian_name' => 'David Johnson',
                'guardian_contact' => '09191234567',
                'address' => '789 Pine Rd, Makati',
            ],
        ];

        foreach ($students as $studentData) {
            Student::firstOrCreate(
                ['student_no' => $studentData['student_no']],
                $studentData
            );
        }

        $this->command->info('Sample data seeded successfully!');
        $this->command->info('Created ' . Package::count() . ' packages');
        $this->command->info('Created ' . Student::count() . ' students');
    }
}

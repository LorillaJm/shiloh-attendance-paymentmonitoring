<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'first_name' => 'Juan',
                'middle_name' => 'Santos',
                'last_name' => 'Dela Cruz',
                'birthdate' => '2010-05-15',
                'sex' => 'Male',
                'address' => '123 Main Street, Quezon City, Metro Manila',
                'guardian_name' => 'Maria Dela Cruz',
                'guardian_contact' => '+63 912 345 6789',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Maria',
                'middle_name' => 'Garcia',
                'last_name' => 'Santos',
                'birthdate' => '2011-08-22',
                'sex' => 'Female',
                'address' => '456 Rizal Avenue, Makati City, Metro Manila',
                'guardian_name' => 'Jose Santos',
                'guardian_contact' => '+63 923 456 7890',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Pedro',
                'middle_name' => 'Reyes',
                'last_name' => 'Gonzales',
                'birthdate' => '2009-12-10',
                'sex' => 'Male',
                'address' => '789 Luna Street, Pasig City, Metro Manila',
                'guardian_name' => 'Ana Gonzales',
                'guardian_contact' => '+63 934 567 8901',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Sofia',
                'middle_name' => 'Cruz',
                'last_name' => 'Ramos',
                'birthdate' => '2012-03-18',
                'sex' => 'Female',
                'address' => '321 Bonifacio Street, Taguig City, Metro Manila',
                'guardian_name' => 'Roberto Ramos',
                'guardian_contact' => '+63 945 678 9012',
                'status' => 'ACTIVE',
            ],
            [
                'first_name' => 'Miguel',
                'middle_name' => 'Torres',
                'last_name' => 'Fernandez',
                'birthdate' => '2010-11-25',
                'sex' => 'Male',
                'address' => '654 Aguinaldo Highway, Cavite',
                'guardian_name' => 'Carmen Fernandez',
                'guardian_contact' => '+63 956 789 0123',
                'status' => 'INACTIVE',
            ],
        ];

        foreach ($students as $studentData) {
            Student::create($studentData);
        }

        $this->command->info('Sample students created successfully!');
        $this->command->info('Created 5 students with auto-generated student numbers.');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Guardian;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ShilohStudentsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        
        // Create 51 students with guardians
        for ($i = 1; $i <= 51; $i++) {
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $birthdate = $faker->dateTimeBetween('-15 years', '-3 years');
            $age = now()->diffInYears($birthdate);

            $student = Student::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => $faker->optional(0.5)->lastName(),
                'birthdate' => $birthdate,
                'sex' => $faker->randomElement(['Male', 'Female']),
                'address' => $faker->address(),
                'guardian_name' => $faker->name(),
                'guardian_contact' => $faker->phoneNumber(),
                'status' => 'ACTIVE',
                'age' => $age,
                'requires_monitoring' => $age <= 10,
            ]);

            // Create guardian user and profile
            $guardianUser = User::create([
                'name' => "Guardian of {$firstName} {$lastName}",
                'email' => strtolower("parent{$i}@shiloh.test"),
                'password' => Hash::make('password'),
                'role' => UserRole::PARENT,
            ]);

            $guardian = Guardian::create([
                'user_id' => $guardianUser->id,
                'first_name' => $faker->firstName(),
                'last_name' => $lastName,
                'middle_name' => $faker->optional(0.3)->lastName(),
                'contact_number' => $faker->phoneNumber(),
                'email' => $guardianUser->email,
                'address' => $student->address,
                'relationship' => $faker->randomElement(['Mother', 'Father', 'Guardian', 'Aunt', 'Uncle']),
            ]);

            // Link guardian to student
            $guardian->students()->attach($student->id, ['is_primary' => true]);
        }
    }
}

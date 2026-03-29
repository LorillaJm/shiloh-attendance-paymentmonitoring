<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Guardian;
use App\Models\Student;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class ParentPortalSeeder extends Seeder
{
    /**
     * Seed the parent portal with test data.
     */
    public function run(): void
    {
        // Create a test parent user
        $parentUser = User::firstOrCreate(
            ['email' => 'parent@test.com'],
            [
                'name' => 'Test Parent',
                'password' => Hash::make('password'),
                'role' => UserRole::PARENT,
            ]
        );

        // Create guardian profile
        $guardian = Guardian::firstOrCreate(
            ['user_id' => $parentUser->id],
            [
                'first_name' => 'Test',
                'last_name' => 'Parent',
                'middle_name' => 'Demo',
                'contact_number' => '09123456789',
                'email' => 'parent@test.com',
                'address' => '123 Test Street, Test City',
                'relationship' => 'Father',
            ]
        );

        // Find or create test students
        $student1 = Student::firstOrCreate(
            ['student_no' => 'TEST-001'],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'middle_name' => 'Test',
                'birthdate' => now()->subYears(8),
                'sex' => 'Male',
                'address' => '123 Test Street, Test City',
                'guardian_name' => 'Test Parent',
                'guardian_contact' => '09123456789',
                'status' => 'ACTIVE',
            ]
        );

        $student2 = Student::firstOrCreate(
            ['student_no' => 'TEST-002'],
            [
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'middle_name' => 'Test',
                'birthdate' => now()->subYears(10),
                'sex' => 'Female',
                'address' => '123 Test Street, Test City',
                'guardian_name' => 'Test Parent',
                'guardian_contact' => '09123456789',
                'status' => 'ACTIVE',
            ]
        );

        // Link students to guardian
        if (!$guardian->students->contains($student1->id)) {
            $guardian->students()->attach($student1->id, ['is_primary' => true]);
        }

        if (!$guardian->students->contains($student2->id)) {
            $guardian->students()->attach($student2->id, ['is_primary' => false]);
        }

        $this->command->info('Parent portal test data seeded successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Email: parent@test.com');
        $this->command->info('Password: password');
    }
}

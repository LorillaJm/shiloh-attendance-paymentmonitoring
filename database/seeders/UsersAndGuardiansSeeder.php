<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Guardian;
use App\Models\Student;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class UsersAndGuardiansSeeder extends Seeder
{
    public function run(): void
    {
        // Create Users for different roles
        $users = [
            // Admin Users
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@shiloh.local',
                'role' => UserRole::ADMIN,
                'password' => 'password123',
            ],
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'juan.delacruz@shiloh.local',
                'role' => UserRole::ADMIN,
                'password' => 'password123',
            ],
            // Parents
            [
                'name' => 'Parent Roberto Cruz',
                'email' => 'parent@shiloh.local',
                'role' => UserRole::PARENT,
                'password' => 'password123',
            ],
            [
                'name' => 'Parent Carmen Lopez',
                'email' => 'carmen.lopez@shiloh.local',
                'role' => UserRole::PARENT,
                'password' => 'password123',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'role' => $userData['role'],
                    'password' => Hash::make($userData['password']),
                ]
            );
        }

        $this->command->info('✓ Created ' . count($users) . ' users across different roles');

        // Create Guardians linked to Parent users
        $parentUsers = User::where('role', UserRole::PARENT)->get();
        
        $guardianDataList = [
            [
                'first_name' => 'Roberto',
                'last_name' => 'Cruz',
                'middle_name' => 'Santos',
                'email' => 'parent@shiloh.local',
                'contact_number' => '+639171234567',
                'address' => '123 Rizal Street, Quezon City',
                'relationship' => 'Father',
            ],
            [
                'first_name' => 'Carmen',
                'last_name' => 'Lopez',
                'middle_name' => 'Reyes',
                'email' => 'carmen.lopez@shiloh.local',
                'contact_number' => '+639182345678',
                'address' => '456 Bonifacio Avenue, Makati City',
                'relationship' => 'Mother',
            ],
        ];

        foreach ($guardianDataList as $index => $guardianData) {
            if (isset($parentUsers[$index])) {
                $guardianData['user_id'] = $parentUsers[$index]->id;
                
                Guardian::firstOrCreate(
                    ['email' => $guardianData['email']],
                    $guardianData
                );
            }
        }

        $this->command->info('✓ Created ' . count($guardianDataList) . ' guardians');

        // Link guardians to students if they exist
        $students = Student::limit(5)->get();
        $guardiansModels = Guardian::all();

        if ($students->count() > 0 && $guardiansModels->count() > 0) {
            foreach ($students as $index => $student) {
                if (isset($guardiansModels[$index])) {
                    // Attach guardian to student
                    $student->guardians()->syncWithoutDetaching([
                        $guardiansModels[$index]->id => [
                            'is_primary' => true,
                        ]
                    ]);
                }
            }
            $this->command->info('✓ Linked guardians to students');
        }

        $this->command->newLine();
        $this->command->info('✅ Seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('📝 Login Credentials (Password: password123):');
        $this->command->info('   👤 Admin: maria.santos@shiloh.local');
        $this->command->info('   👤 Admin: juan.delacruz@shiloh.local');
        $this->command->info('   👨‍👩‍👧 Parent: parent@shiloh.local');
        $this->command->info('   👨‍👩‍👧 Parent: carmen.lopez@shiloh.local');
    }
}

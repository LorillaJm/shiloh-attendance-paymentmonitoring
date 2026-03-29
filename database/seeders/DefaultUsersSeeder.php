<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guardian;
use App\Models\Student;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating default users...');

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@shiloh.local'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
            ]
        );

        $this->command->info('✓ Admin user created');

        // Create Parent Users with Guardians
        $parentData = [
            [
                'user' => [
                    'name' => 'Maria Santos',
                    'email' => 'maria@shiloh.local',
                    'password' => 'password',
                    'role' => UserRole::PARENT,
                ],
                'guardian' => [
                    'first_name' => 'Maria',
                    'last_name' => 'Santos',
                    'middle_name' => 'Cruz',
                    'contact_number' => '+639171234567',
                    'email' => 'maria@shiloh.local',
                    'address' => '123 Rizal Street, Quezon City',
                    'relationship' => 'Mother',
                ],
            ],
            [
                'user' => [
                    'name' => 'Juan Dela Cruz',
                    'email' => 'juan@shiloh.local',
                    'password' => 'password',
                    'role' => UserRole::PARENT,
                ],
                'guardian' => [
                    'first_name' => 'Juan',
                    'last_name' => 'Dela Cruz',
                    'middle_name' => 'Santos',
                    'contact_number' => '+639182345678',
                    'email' => 'juan@shiloh.local',
                    'address' => '456 Bonifacio Avenue, Makati City',
                    'relationship' => 'Father',
                ],
            ],
            [
                'user' => [
                    'name' => 'Ana Reyes',
                    'email' => 'ana@shiloh.local',
                    'password' => 'password',
                    'role' => UserRole::PARENT,
                ],
                'guardian' => [
                    'first_name' => 'Ana',
                    'last_name' => 'Reyes',
                    'middle_name' => 'Garcia',
                    'contact_number' => '+639193456789',
                    'email' => 'ana@shiloh.local',
                    'address' => '789 Mabini Street, Manila',
                    'relationship' => 'Mother',
                ],
            ],
        ];

        foreach ($parentData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['user']['email']],
                [
                    'name' => $data['user']['name'],
                    'password' => Hash::make($data['user']['password']),
                    'role' => $data['user']['role'],
                ]
            );

            $guardian = Guardian::firstOrCreate(
                ['email' => $data['guardian']['email']],
                array_merge($data['guardian'], ['user_id' => $user->id])
            );

            $this->command->info("✓ Parent user created: {$user->email}");
        }

        // Link guardians to existing students if any exist
        $students = Student::limit(3)->get();
        $guardians = Guardian::all();

        if ($students->count() > 0 && $guardians->count() > 0) {
            foreach ($students as $index => $student) {
                if (isset($guardians[$index])) {
                    $student->guardians()->syncWithoutDetaching([
                        $guardians[$index]->id => ['is_primary' => true]
                    ]);
                    $this->command->info("✓ Linked {$student->full_name} to {$guardians[$index]->full_name}");
                }
            }
        }

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('           DEFAULT USERS CREATED SUCCESSFULLY');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
        
        $this->command->info('🔐 LOGIN CREDENTIALS (All passwords: password)');
        $this->command->newLine();
        
        $this->command->info('👤 ADMIN ACCESS:');
        $this->command->info('   URL: /admin/login');
        $this->command->info('   Email: admin@shiloh.local');
        $this->command->info('   Password: password');
        $this->command->newLine();
        
        $this->command->info('👨‍👩‍👧 PARENT ACCESS:');
        $this->command->info('   URL: /parent/login');
        $this->command->info('   Email: maria@shiloh.local');
        $this->command->info('   Email: juan@shiloh.local');
        $this->command->info('   Email: ana@shiloh.local');
        $this->command->info('   Password: password (for all)');
        $this->command->newLine();
        
        $this->command->warn('⚠️  IMPORTANT: Change these passwords in production!');
        $this->command->newLine();
    }
}

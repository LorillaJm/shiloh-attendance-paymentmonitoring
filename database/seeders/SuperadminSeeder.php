<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates an initial superadmin user for system administration.
     * 
     * IMPORTANT: Change the password immediately after first login!
     * 
     * Default Credentials:
     * - Email: superadmin@shiloh.edu
     * - Password: changeme
     */
    public function run(): void
    {
        // Check if superadmin already exists
        $existingSuperadmin = User::where('role', UserRole::SUPERADMIN)->first();
        
        if ($existingSuperadmin) {
            $this->command->info('Superadmin user already exists: ' . $existingSuperadmin->email);
            return;
        }
        
        // Create superadmin user
        $superadmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@shiloh.edu',
            'password' => Hash::make('changeme'),
            'role' => UserRole::SUPERADMIN,
            'theme' => 'light',
            'email_verified_at' => now(),
        ]);
        
        $this->command->info('✓ Superadmin user created successfully!');
        $this->command->newLine();
        $this->command->warn('═══════════════════════════════════════════════════════════');
        $this->command->warn('  IMPORTANT: CHANGE PASSWORD ON FIRST LOGIN!');
        $this->command->warn('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->info('Default Login Credentials:');
        $this->command->info('  Email:    superadmin@shiloh.edu');
        $this->command->info('  Password: changeme');
        $this->command->newLine();
        $this->command->warn('For security reasons, please change this password immediately');
        $this->command->warn('after your first login to the system.');
        $this->command->newLine();
    }
}

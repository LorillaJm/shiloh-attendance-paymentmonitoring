<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@shiloh.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'ADMIN',
            ]
        );

        // Update role if user already exists
        if ($admin->role !== 'ADMIN') {
            $admin->update(['role' => 'ADMIN']);
        }

        $this->command->info('=== Admin User Created ===');
        $this->command->info('Email: admin@shiloh.local');
        $this->command->info('Password: password');
        $this->command->info('Role: ADMIN');
        $this->command->info('');
        $this->command->info('Login at: /admin/login');
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT: Change the password after first login!');
    }
}

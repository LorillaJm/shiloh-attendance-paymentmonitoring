<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        User::firstOrCreate(
            ['email' => 'admin@shiloh.local'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin123!'),
                'role' => UserRole::ADMIN,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@shiloh.local');
        $this->command->info('Password: Admin123!');
    }
}

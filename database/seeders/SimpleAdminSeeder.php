<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SimpleAdminSeeder extends Seeder
{
    /**
     * Create a simple admin user for easy login.
     */
    public function run(): void
    {
        // Create simple admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin'),
            ]
        );

        $this->command->info('=== Simple Admin User Created ===');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Password: admin');
        $this->command->info('');
        $this->command->info('Login at: /admin/login');
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT: This is for development only!');
        $this->command->warn('⚠️  Change credentials in production!');
    }
}

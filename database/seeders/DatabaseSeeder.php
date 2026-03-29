<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SuperadminSeeder::class, // Create superadmin first
            AdminSeeder::class,
            SessionTypeSeeder::class,
            ShilohStudentsSeeder::class,
            // RealisticDataSeeder::class, // Comment out old seeder
        ]);
    }
}

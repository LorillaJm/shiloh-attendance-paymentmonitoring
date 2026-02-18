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
            AdminSeeder::class,
            SessionTypeSeeder::class,
            TeacherSeeder::class,
            ShilohStudentsSeeder::class,
            // RealisticDataSeeder::class, // Comment out old seeder
        ]);
    }
}

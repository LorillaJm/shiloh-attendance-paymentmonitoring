<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            ['name' => 'Teacher Maria Santos', 'email' => 'maria@shiloh.test'],
            ['name' => 'Teacher Juan Dela Cruz', 'email' => 'juan@shiloh.test'],
            ['name' => 'Teacher Ana Reyes', 'email' => 'ana@shiloh.test'],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => UserRole::TEACHER,
            ]);
        }
    }
}

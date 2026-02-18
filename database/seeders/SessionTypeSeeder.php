<?php

namespace Database\Seeders;

use App\Models\SessionType;
use Illuminate\Database\Seeder;

class SessionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $sessionTypes = [
            [
                'name' => 'One-on-One',
                'code' => 'ONE_ON_ONE',
                'description' => 'Individual therapy session',
                'default_duration_minutes' => 60,
                'requires_monitoring' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Group Play',
                'code' => 'GROUP_PLAY',
                'description' => 'Group play therapy session',
                'default_duration_minutes' => 90,
                'requires_monitoring' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Writing',
                'code' => 'WRITING',
                'description' => 'Writing skills development',
                'default_duration_minutes' => 45,
                'requires_monitoring' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Practice Speech',
                'code' => 'PRACTICE_SPEECH',
                'description' => 'Speech therapy and practice',
                'default_duration_minutes' => 60,
                'requires_monitoring' => true,
                'is_active' => true,
            ],
        ];

        foreach ($sessionTypes as $type) {
            SessionType::create($type);
        }
    }
}

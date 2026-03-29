<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:fix-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invalid user roles in the database (convert USER to PARENT)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for users with invalid roles...');

        // Get all users with invalid roles using raw query to avoid enum casting
        $invalidUsers = DB::table('users')
            ->whereNotIn('role', ['SUPERADMIN', 'ADMIN', 'PARENT'])
            ->get();

        if ($invalidUsers->isEmpty()) {
            $this->info('✓ No users with invalid roles found.');
            return 0;
        }

        $this->warn("Found {$invalidUsers->count()} user(s) with invalid roles:");

        foreach ($invalidUsers as $user) {
            $this->line("  - {$user->name} ({$user->email}): {$user->role}");
        }

        if (!$this->confirm('Convert all invalid roles to PARENT?', true)) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Update invalid roles to PARENT
        $updated = DB::table('users')
            ->whereNotIn('role', ['SUPERADMIN', 'ADMIN', 'PARENT'])
            ->update(['role' => 'PARENT']);

        $this->info("✓ Updated {$updated} user(s) to PARENT role.");
        
        return 0;
    }
}

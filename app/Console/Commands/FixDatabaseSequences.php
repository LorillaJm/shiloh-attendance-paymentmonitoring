<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDatabaseSequences extends Command
{
    protected $signature = 'db:fix-sequences';
    protected $description = 'Fix PostgreSQL sequences that are out of sync';

    public function handle()
    {
        $this->info('Fixing database sequences...');

        try {
            // Fix users table sequence
            DB::statement("SELECT setval('users_id_seq', (SELECT MAX(id) FROM users));");
            $this->info('✓ Fixed users_id_seq');

            // Fix other common tables
            $tables = ['students', 'guardians', 'enrollments', 'payment_schedules', 'session_occurrences'];
            
            foreach ($tables as $table) {
                try {
                    DB::statement("SELECT setval('{$table}_id_seq', (SELECT COALESCE(MAX(id), 1) FROM {$table}));");
                    $this->info("✓ Fixed {$table}_id_seq");
                } catch (\Exception $e) {
                    $this->warn("⚠ Could not fix {$table}_id_seq: " . $e->getMessage());
                }
            }

            $this->info('');
            $this->info('All sequences have been fixed!');
            
        } catch (\Exception $e) {
            $this->error('Error fixing sequences: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

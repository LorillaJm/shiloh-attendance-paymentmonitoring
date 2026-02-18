<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ShilohSetup extends Command
{
    protected $signature = 'shiloh:setup {--fresh : Fresh migration}';
    protected $description = 'Setup Shiloh system with all required data';

    public function handle(): int
    {
        $this->info('🚀 Setting up Shiloh Learning & Development Center System...');

        if ($this->option('fresh')) {
            $this->warn('⚠️  Running fresh migration (all data will be lost)');
            if (!$this->confirm('Are you sure?')) {
                return Command::FAILURE;
            }
            Artisan::call('migrate:fresh');
            $this->info('✓ Fresh migration completed');
        } else {
            Artisan::call('migrate');
            $this->info('✓ Migrations completed');
        }

        $this->info('📚 Seeding session types...');
        Artisan::call('db:seed', ['--class' => 'SessionTypeSeeder']);
        
        $this->info('👨‍🏫 Seeding teachers...');
        Artisan::call('db:seed', ['--class' => 'TeacherSeeder']);
        
        $this->info('👨‍👩‍👧‍👦 Seeding 51 students with guardians...');
        Artisan::call('db:seed', ['--class' => 'ShilohStudentsSeeder']);
        
        $this->info('📅 Generating session occurrences for next 30 days...');
        Artisan::call('sessions:generate', ['--days' => 30]);

        $this->newLine();
        $this->info('✅ Setup completed successfully!');
        $this->newLine();
        $this->info('Default Credentials:');
        $this->line('  Teachers: maria@shiloh.test, juan@shiloh.test, ana@shiloh.test');
        $this->line('  Parents: parent1@shiloh.test to parent51@shiloh.test');
        $this->line('  Password: password');
        $this->newLine();
        $this->info('Next Steps:');
        $this->line('  1. Create student schedules via admin panel');
        $this->line('  2. Generate more session occurrences as needed');
        $this->line('  3. Setup cron for automated tasks: php artisan schedule:run');

        return Command::SUCCESS;
    }
}

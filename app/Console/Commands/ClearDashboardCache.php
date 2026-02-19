<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearDashboardCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all dashboard caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing dashboard caches...');

        try {
            \App\Services\DashboardCacheService::clearAll();
            
            $this->info('✓ Dashboard caches cleared successfully!');
            $this->line('');
            $this->line('Cleared caches:');
            $this->line('  - KPI Stats');
            $this->line('  - Collections Trend');
            $this->line('  - Alerts');
            $this->line('  - Recent Payments');
            $this->line('  - Attendance Snapshot');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to clear dashboard caches: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WarmDashboardCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:warm-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warm up dashboard caches by pre-loading all widget data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Warming up dashboard caches...');

        try {
            \App\Services\DashboardCacheService::warmUp();
            
            $this->info('✓ Dashboard caches warmed successfully!');
            $this->line('');
            $this->line('Cached data:');
            $this->line('  - KPI Stats (5 min TTL)');
            $this->line('  - Collections Trend (5 min TTL)');
            $this->line('  - Alerts (5 min TTL)');
            $this->line('  - Recent Payments (3 min TTL)');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to warm dashboard caches: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

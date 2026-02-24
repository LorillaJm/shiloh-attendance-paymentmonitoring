<?php

namespace App\Console\Commands;

use App\Services\DashboardCacheService;
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
    protected $description = 'Warm up dashboard caches for optimal performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Warming up dashboard caches...');
        
        try {
            DashboardCacheService::warmUp();
            
            $this->info('✓ Dashboard caches warmed up successfully');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to warm up caches: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

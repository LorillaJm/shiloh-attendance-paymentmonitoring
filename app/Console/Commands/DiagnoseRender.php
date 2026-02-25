<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DiagnoseRender extends Command
{
    protected $signature = 'diagnose:render';
    protected $description = 'Diagnose Render deployment issues';

    public function handle(): int
    {
        $this->info('🔍 Diagnosing Render deployment...');
        $this->newLine();

        // Check APP_KEY
        $this->info('1. Checking APP_KEY...');
        if (empty(config('app.key'))) {
            $this->error('   ✗ APP_KEY is not set!');
            $this->warn('   Run: php artisan key:generate');
        } else {
            $this->info('   ✓ APP_KEY is set');
        }
        $this->newLine();

        // Check database connection
        $this->info('2. Checking database connection...');
        try {
            DB::connection()->getPdo();
            $this->info('   ✓ Database connected');
            
            // Check tables
            $tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
            $this->info('   ✓ Found ' . count($tables) . ' tables');
        } catch (\Exception $e) {
            $this->error('   ✗ Database connection failed: ' . $e->getMessage());
        }
        $this->newLine();

        // Check cache
        $this->info('3. Checking cache...');
        try {
            Cache::put('test', 'value', 60);
            $value = Cache::get('test');
            Cache::forget('test');
            $this->info('   ✓ Cache working (Driver: ' . config('cache.default') . ')');
        } catch (\Exception $e) {
            $this->error('   ✗ Cache error: ' . $e->getMessage());
        }
        $this->newLine();

        // Check storage permissions
        $this->info('4. Checking storage permissions...');
        $paths = [
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
        ];
        
        foreach ($paths as $path) {
            if (is_writable($path)) {
                $this->info("   ✓ {$path} is writable");
            } else {
                $this->error("   ✗ {$path} is NOT writable");
            }
        }
        $this->newLine();

        // Check environment
        $this->info('5. Environment configuration:');
        $this->info('   APP_ENV: ' . config('app.env'));
        $this->info('   APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false'));
        $this->info('   APP_URL: ' . config('app.url'));
        $this->info('   DB_CONNECTION: ' . config('database.default'));
        $this->newLine();

        $this->info('✅ Diagnosis complete!');
        
        return Command::SUCCESS;
    }
}

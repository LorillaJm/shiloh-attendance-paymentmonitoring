<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DiagnosePerformance extends Command
{
    protected $signature = 'app:diagnose-performance';
    protected $description = 'Diagnose performance issues in the application';

    public function handle()
    {
        $this->info('🔍 Performance Diagnostics Report');
        $this->newLine();

        // Check database indexes
        $this->checkDatabaseIndexes();
        
        // Check cache configuration
        $this->checkCacheConfig();
        
        // Check environment settings
        $this->checkEnvironment();
        
        // Check asset compilation
        $this->checkAssets();
        
        $this->newLine();
        $this->info('✅ Diagnostics complete!');
    }

    private function checkDatabaseIndexes()
    {
        $this->info('📊 Database Indexes:');
        
        $tables = ['payment_schedules', 'students', 'enrollments', 'attendance_records'];
        
        foreach ($tables as $table) {
            try {
                $indexes = DB::select("SELECT indexname FROM pg_indexes WHERE tablename = ?", [$table]);
                $this->line("  {$table}: " . count($indexes) . " indexes");
            } catch (\Exception $e) {
                $this->warn("  {$table}: Could not check indexes");
            }
        }
        
        $this->newLine();
    }

    private function checkCacheConfig()
    {
        $this->info('💾 Cache Configuration:');
        $this->line('  Driver: ' . config('cache.default'));
        $this->line('  Prefix: ' . config('cache.prefix'));
        
        try {
            Cache::put('test_key', 'test_value', 10);
            $value = Cache::get('test_key');
            $this->line('  Status: ' . ($value === 'test_value' ? '✅ Working' : '❌ Not working'));
            Cache::forget('test_key');
        } catch (\Exception $e) {
            $this->error('  Status: ❌ Error - ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    private function checkEnvironment()
    {
        $this->info('⚙️  Environment Settings:');
        $this->line('  APP_ENV: ' . config('app.env'));
        $this->line('  APP_DEBUG: ' . (config('app.debug') ? '❌ TRUE (should be false in production)' : '✅ FALSE'));
        $this->line('  DB_CONNECTION: ' . config('database.default'));
        
        $this->newLine();
    }

    private function checkAssets()
    {
        $this->info('📦 Asset Compilation:');
        
        $manifestPath = public_path('build/manifest.json');
        if (file_exists($manifestPath)) {
            $this->line('  Build manifest: ✅ Found');
            $manifest = json_decode(file_get_contents($manifestPath), true);
            $this->line('  Assets: ' . count($manifest) . ' files');
        } else {
            $this->warn('  Build manifest: ❌ Not found - Run: npm run build');
        }
        
        $this->newLine();
    }
}

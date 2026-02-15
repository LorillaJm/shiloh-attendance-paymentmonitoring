<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseHealthCheck extends Command
{
    protected $signature = 'db:health-check 
                            {--detailed : Show detailed connection information}
                            {--test-query : Run test queries to measure performance}';

    protected $description = 'Check database connectivity and measure response time';

    public function handle(): int
    {
        $this->info('🔍 Database Health Check');
        $this->newLine();

        // Basic connection info
        $this->checkConnectionInfo();
        $this->newLine();

        // Test connectivity
        if (!$this->testConnectivity()) {
            return Command::FAILURE;
        }

        // Test response time
        $this->testResponseTime();
        $this->newLine();

        // Detailed info
        if ($this->option('detailed')) {
            $this->showDetailedInfo();
            $this->newLine();
        }

        // Test queries
        if ($this->option('test-query')) {
            $this->runTestQueries();
            $this->newLine();
        }

        // Recommendations
        $this->showRecommendations();

        return Command::SUCCESS;
    }

    private function checkConnectionInfo(): void
    {
        $this->line('📊 Connection Configuration:');
        
        $options = config('database.connections.pgsql.options', []);
        
        $this->table(
            ['Setting', 'Value'],
            [
                ['Driver', config('database.default')],
                ['Host', config('database.connections.pgsql.host')],
                ['Port', config('database.connections.pgsql.port')],
                ['Database', config('database.connections.pgsql.database')],
                ['SSL Mode', config('database.connections.pgsql.sslmode')],
                ['Timeout', $options[\PDO::ATTR_TIMEOUT] ?? 'default'],
                ['Persistent', ($options[\PDO::ATTR_PERSISTENT] ?? false) ? 'Yes' : 'No'],
            ]
        );
    }

    private function testConnectivity(): bool
    {
        $this->line('🔌 Testing Connectivity...');

        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $connectionTime = round((microtime(true) - $start) * 1000, 2);

            $this->info("✅ Connected successfully in {$connectionTime}ms");
            
            if ($connectionTime > 100) {
                $this->warn("⚠️  Connection time is high (>{$connectionTime}ms). Consider using connection pooler.");
            } elseif ($connectionTime > 50) {
                $this->comment("⚡ Connection time is acceptable ({$connectionTime}ms)");
            } else {
                $this->info("⚡ Connection time is excellent ({$connectionTime}ms)");
            }

            return true;
        } catch (\Exception $e) {
            $this->error('❌ Connection failed: ' . $e->getMessage());
            return false;
        }
    }

    private function testResponseTime(): void
    {
        $this->line('⏱️  Testing Query Response Time...');

        try {
            // Simple query
            $start = microtime(true);
            DB::select('SELECT 1 as test');
            $simpleQueryTime = round((microtime(true) - $start) * 1000, 2);

            // Count query
            $start = microtime(true);
            DB::table('students')->count();
            $countQueryTime = round((microtime(true) - $start) * 1000, 2);

            $this->table(
                ['Query Type', 'Response Time', 'Status'],
                [
                    ['Simple SELECT', "{$simpleQueryTime}ms", $this->getStatusEmoji($simpleQueryTime)],
                    ['COUNT Query', "{$countQueryTime}ms", $this->getStatusEmoji($countQueryTime)],
                ]
            );

            $avgTime = ($simpleQueryTime + $countQueryTime) / 2;
            
            if ($avgTime < 50) {
                $this->info("✅ Average response time: {$avgTime}ms (Excellent)");
            } elseif ($avgTime < 100) {
                $this->comment("⚡ Average response time: {$avgTime}ms (Good)");
            } else {
                $this->warn("⚠️  Average response time: {$avgTime}ms (Needs optimization)");
            }

        } catch (\Exception $e) {
            $this->error('❌ Query test failed: ' . $e->getMessage());
        }
    }

    private function showDetailedInfo(): void
    {
        $this->line('📋 Detailed Database Information:');

        try {
            // PostgreSQL version
            $version = DB::select("SELECT version()")[0]->version;
            $this->line("PostgreSQL Version: {$version}");
            $this->newLine();

            // Current connections
            $connections = DB::select("
                SELECT count(*) as total 
                FROM pg_stat_activity 
                WHERE datname = current_database()
            ")[0]->total;
            $this->line("Active Connections: {$connections}");

            // Database size
            $size = DB::select("
                SELECT pg_size_pretty(pg_database_size(current_database())) as size
            ")[0]->size;
            $this->line("Database Size: {$size}");

            // Table count
            $tables = DB::select("
                SELECT count(*) as total 
                FROM information_schema.tables 
                WHERE table_schema = 'public'
            ")[0]->total;
            $this->line("Tables: {$tables}");

        } catch (\Exception $e) {
            $this->error('Could not fetch detailed info: ' . $e->getMessage());
        }
    }

    private function runTestQueries(): void
    {
        $this->line('🧪 Running Test Queries...');

        $tests = [
            'Students Count' => fn() => DB::table('students')->count(),
            'Enrollments Count' => fn() => DB::table('enrollments')->count(),
            'Payment Schedules Count' => fn() => DB::table('payment_schedules')->count(),
            'Attendance Records Count' => fn() => DB::table('attendance_records')->count(),
        ];

        $results = [];

        foreach ($tests as $name => $query) {
            try {
                $start = microtime(true);
                $result = $query();
                $time = round((microtime(true) - $start) * 1000, 2);
                
                $results[] = [
                    $name,
                    $result,
                    "{$time}ms",
                    $this->getStatusEmoji($time)
                ];
            } catch (\Exception $e) {
                $results[] = [$name, 'Error', $e->getMessage(), '❌'];
            }
        }

        $this->table(['Query', 'Result', 'Time', 'Status'], $results);
    }

    private function showRecommendations(): void
    {
        $this->newLine();
        $this->line('💡 Recommendations:');

        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port');

        // Check region
        if (str_contains($host, 'ap-northeast-1')) {
            $this->warn('⚠️  You are using Tokyo (ap-northeast-1) region');
            $this->line('   For Philippines users, migrate to Singapore (ap-southeast-1)');
            $this->line('   Expected latency reduction: 50-70% (from ~80-100ms to ~30-50ms)');
            $this->newLine();
        }

        // Check pooler
        if ($port == 5432 && str_contains($host, 'pooler')) {
            $this->comment('💡 You are using Session Pooler (port 5432)');
            $this->line('   For production, consider Transaction Pooler (port 6543)');
            $this->line('   Benefits: Better connection handling, lower overhead');
            $this->newLine();
        } elseif ($port == 6543) {
            $this->info('✅ Using Transaction Pooler (port 6543) - Optimal for production');
            $this->newLine();
        } elseif ($port == 5432 && !str_contains($host, 'pooler')) {
            $this->warn('⚠️  Using direct connection (port 5432)');
            $this->line('   For production, use Transaction Pooler:');
            $this->line('   DB_HOST=aws-1-ap-northeast-1.pooler.supabase.com');
            $this->line('   DB_PORT=6543');
            $this->newLine();
        }

        // Check SSL
        $sslmode = config('database.connections.pgsql.sslmode');
        if ($sslmode !== 'require') {
            $this->warn('⚠️  SSL mode is not set to "require"');
            $this->line('   Set DB_SSLMODE=require for Supabase');
            $this->newLine();
        }

        // General tips
        $this->line('📌 Performance Tips:');
        $this->line('   1. Use indexes on frequently queried columns');
        $this->line('   2. Enable query caching for repeated queries');
        $this->line('   3. Use eager loading to prevent N+1 queries');
        $this->line('   4. Monitor slow queries with EXPLAIN ANALYZE');
        $this->line('   5. Consider Redis for session/cache in production');
    }

    private function getStatusEmoji(float $time): string
    {
        if ($time < 50) {
            return '🟢 Excellent';
        } elseif ($time < 100) {
            return '🟡 Good';
        } elseif ($time < 200) {
            return '🟠 Fair';
        } else {
            return '🔴 Slow';
        }
    }
}

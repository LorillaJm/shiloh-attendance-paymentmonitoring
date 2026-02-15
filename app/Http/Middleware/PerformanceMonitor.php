<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitor
{
    private $startTime;
    private $startMemory;
    private $queryCount = 0;
    private $slowQueries = [];

    public function handle(Request $request, Closure $next): Response
    {
        // Only monitor in local/development
        if (!app()->environment('local', 'development')) {
            return $next($request);
        }

        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage();
        
        // Enable query logging
        DB::enableQueryLog();
        
        // Listen for queries
        DB::listen(function ($query) {
            $this->queryCount++;
            
            // Log slow queries (> 200ms)
            if ($query->time > 200) {
                $this->slowQueries[] = [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ];
            }
        });

        $response = $next($request);

        $this->logPerformance($request);

        return $response;
    }

    private function logPerformance(Request $request): void
    {
        $duration = round((microtime(true) - $this->startTime) * 1000, 2);
        $memoryUsed = round((memory_get_usage() - $this->startMemory) / 1024 / 1024, 2);
        $memoryPeak = round(memory_get_peak_usage() / 1024 / 1024, 2);

        $data = [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'route' => $request->route()?->getName() ?? 'N/A',
            'action' => $request->route()?->getActionName() ?? 'N/A',
            'duration_ms' => $duration,
            'query_count' => $this->queryCount,
            'memory_used_mb' => $memoryUsed,
            'memory_peak_mb' => $memoryPeak,
            'slow_queries_count' => count($this->slowQueries),
        ];

        // Log warning if request is slow
        if ($duration > 3000) {
            Log::warning('🐌 VERY SLOW REQUEST (>3s)', $data);
            
            // Log slow queries
            if (!empty($this->slowQueries)) {
                Log::warning('Slow Queries:', [
                    'queries' => array_map(function ($query) {
                        return [
                            'sql' => $this->sanitizeSql($query['sql']),
                            'time_ms' => $query['time'],
                        ];
                    }, $this->slowQueries)
                ]);
            }
        } elseif ($duration > 1000) {
            Log::info('⚠️  SLOW REQUEST (>1s)', $data);
        } elseif ($duration > 500) {
            Log::debug('⏱️  Request took >500ms', $data);
        }

        // Log if too many queries
        if ($this->queryCount > 50) {
            Log::warning('🔥 HIGH QUERY COUNT', [
                'url' => $request->fullUrl(),
                'query_count' => $this->queryCount,
                'duration_ms' => $duration,
            ]);
        }
    }

    private function sanitizeSql(string $sql): string
    {
        // Remove sensitive data patterns
        $sql = preg_replace('/password\s*=\s*[\'"][^\'"]*[\'"]/', 'password=***', $sql);
        $sql = preg_replace('/email\s*=\s*[\'"][^\'"]*[\'"]/', 'email=***', $sql);
        return $sql;
    }
}

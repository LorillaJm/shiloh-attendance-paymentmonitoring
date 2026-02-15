<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AnalyzePerformance extends Command
{
    protected $signature = 'app:analyze-performance {--lines=100}';
    protected $description = 'Analyze performance logs and show slowest requests';

    public function handle()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            $this->error('Log file not found: ' . $logFile);
            return 1;
        }

        $this->info('📊 Performance Analysis Report');
        $this->newLine();

        $lines = (int) $this->option('lines');
        $content = $this->tail($logFile, $lines);
        
        $slowRequests = [];
        $highQueryCounts = [];
        
        // Parse log entries
        $logLines = explode("\n", $content);
        foreach ($logLines as $line) {
            // Find slow requests
            if (str_contains($line, 'SLOW REQUEST') || str_contains($line, 'VERY SLOW REQUEST')) {
                if (preg_match('/duration_ms["\']:\s*([0-9.]+)/', $line, $matches)) {
                    $duration = (float) $matches[1];
                    if (preg_match('/url["\']:\s*["\']([^"\']+)/', $line, $urlMatches)) {
                        $slowRequests[] = [
                            'url' => $urlMatches[1],
                            'duration' => $duration,
                        ];
                    }
                }
            }
            
            // Find high query counts
            if (str_contains($line, 'HIGH QUERY COUNT')) {
                if (preg_match('/query_count["\']:\s*([0-9]+)/', $line, $matches)) {
                    $count = (int) $matches[1];
                    if (preg_match('/url["\']:\s*["\']([^"\']+)/', $line, $urlMatches)) {
                        $highQueryCounts[] = [
                            'url' => $urlMatches[1],
                            'count' => $count,
                        ];
                    }
                }
            }
        }

        // Display slowest requests
        if (!empty($slowRequests)) {
            $this->info('🐌 Slowest Requests:');
            usort($slowRequests, fn($a, $b) => $b['duration'] <=> $a['duration']);
            
            foreach (array_slice($slowRequests, 0, 10) as $request) {
                $this->line(sprintf(
                    '  %s ms - %s',
                    number_format($request['duration'], 0),
                    $request['url']
                ));
            }
            $this->newLine();
        } else {
            $this->info('✅ No slow requests found in recent logs');
            $this->newLine();
        }

        // Display high query counts
        if (!empty($highQueryCounts)) {
            $this->info('🔥 High Query Counts:');
            usort($highQueryCounts, fn($a, $b) => $b['count'] <=> $a['count']);
            
            foreach (array_slice($highQueryCounts, 0, 10) as $request) {
                $this->line(sprintf(
                    '  %d queries - %s',
                    $request['count'],
                    $request['url']
                ));
            }
            $this->newLine();
        }

        // Show recommendations
        $this->info('💡 Recommendations:');
        if (!empty($slowRequests)) {
            $this->line('  - Check slow queries in the log');
            $this->line('  - Ensure all indexes are created');
            $this->line('  - Enable query caching');
        }
        if (!empty($highQueryCounts)) {
            $this->line('  - Look for N+1 query issues');
            $this->line('  - Add eager loading with ->with()');
        }
        if (empty($slowRequests) && empty($highQueryCounts)) {
            $this->line('  - Performance looks good!');
            $this->line('  - Continue monitoring');
        }

        return 0;
    }

    private function tail(string $file, int $lines): string
    {
        $handle = fopen($file, 'r');
        if (!$handle) {
            return '';
        }

        $buffer = 4096;
        $output = '';
        $chunk = '';

        fseek($handle, -1, SEEK_END);
        
        if (fread($handle, 1) != "\n") {
            $lines -= 1;
        }

        $output = '';
        while (ftell($handle) > 0 && $lines >= 0) {
            $seek = min(ftell($handle), $buffer);
            fseek($handle, -$seek, SEEK_CUR);
            $chunk = fread($handle, $seek);
            fseek($handle, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            $output = $chunk . $output;
            $lines -= substr_count($chunk, "\n");
        }

        fclose($handle);

        return $output;
    }
}

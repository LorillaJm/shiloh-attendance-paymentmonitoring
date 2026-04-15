<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DatabaseTableExport;
use Carbon\Carbon;

class DatabaseBackupService
{
    protected string $basePath;
    protected string $timezone;
    protected array $tables;
    protected int $chunkSize;
    protected int $retentionMonths;

    public function __construct()
    {
        $this->basePath = base_path(config('backup.path', 'data backup'));
        $this->timezone = config('backup.timezone', 'Asia/Manila');
        $this->tables = config('backup.tables', []);
        $this->chunkSize = config('backup.chunk_size', 1000);
        $this->retentionMonths = config('backup.retention_months', 12);
    }

    /**
     * Run a full backup of all configured tables.
     * Returns an associative summary array.
     */
    public function run(): array
    {
        $startedAt = Carbon::now($this->timezone);
        $folderName = $startedAt->format('Y-m');
        $backupDir = $this->basePath . DIRECTORY_SEPARATOR . $folderName;

        $summary = [
            'started_at' => $startedAt->toDateTimeString(),
            'finished_at' => null,
            'folder' => $folderName,
            'full_path' => $backupDir,
            'status' => 'running',
            'tables' => [],
            'total_tables' => 0,
            'total_rows' => 0,
            'errors' => [],
        ];

        // Prevent duplicate overlapping runs via a simple lock file
        $lockFile = $this->basePath . DIRECTORY_SEPARATOR . '.backup.lock';
        if (File::exists($lockFile)) {
            $lockAge = Carbon::createFromTimestamp(File::lastModified($lockFile));
            // If lock older than 30 minutes, assume stale and remove
            if ($lockAge->diffInMinutes(now()) < 30) {
                $summary['status'] = 'skipped';
                $summary['errors'][] = 'Another backup is already running (lock file exists).';
                Log::warning('DatabaseBackup: Skipped — lock file exists.');
                return $summary;
            }
            File::delete($lockFile);
        }

        try {
            // Create directories
            File::ensureDirectoryExists($backupDir, 0755, true);
            File::put($lockFile, $startedAt->toDateTimeString());

            $tables = $this->getTables();
            $summary['total_tables'] = count($tables);

            foreach ($tables as $table) {
                $tableSummary = $this->exportTable($table, $backupDir);
                $summary['tables'][$table] = $tableSummary;
                $summary['total_rows'] += $tableSummary['rows'];

                if ($tableSummary['status'] === 'error') {
                    $summary['errors'][] = "{$table}: {$tableSummary['error']}";
                }
            }

            $summary['status'] = empty($summary['errors']) ? 'success' : 'partial';
            $summary['finished_at'] = Carbon::now($this->timezone)->toDateTimeString();

            // Write summary JSON
            $summaryPath = $backupDir . DIRECTORY_SEPARATOR . 'backup-summary.json';
            File::put($summaryPath, json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Write log file
            $this->writeLogFile($backupDir, $summary);

            // Retention cleanup
            if ($this->retentionMonths > 0) {
                $this->cleanOldBackups();
            }

            Log::info("DatabaseBackup: Completed — {$summary['total_tables']} tables, {$summary['total_rows']} rows.");

        } catch (\Throwable $e) {
            $summary['status'] = 'failed';
            $summary['errors'][] = $e->getMessage();
            $summary['finished_at'] = Carbon::now($this->timezone)->toDateTimeString();

            Log::error('DatabaseBackup: Failed — ' . $e->getMessage());

            // Try to write summary even on failure
            try {
                File::ensureDirectoryExists($backupDir, 0755, true);
                File::put(
                    $backupDir . DIRECTORY_SEPARATOR . 'backup-summary.json',
                    json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                );
            } catch (\Throwable $ignored) {
            }
        } finally {
            // Release lock
            if (File::exists($lockFile)) {
                File::delete($lockFile);
            }
        }

        return $summary;
    }

    /**
     * Export a single table to an xlsx file.
     */
    protected function exportTable(string $table, string $dir): array
    {
        $result = [
            'rows' => 0,
            'file' => "{$table}.xlsx",
            'status' => 'success',
            'error' => null,
        ];

        try {
            $filePath = $dir . DIRECTORY_SEPARATOR . "{$table}.xlsx";
            $rowCount = DB::table($table)->count();
            $result['rows'] = $rowCount;

            $content = Excel::raw(
                new DatabaseTableExport($table, $this->chunkSize),
                \Maatwebsite\Excel\Excel::XLSX
            );

            File::put($filePath, $content);
        } catch (\Throwable $e) {
            $result['status'] = 'error';
            $result['error'] = $e->getMessage();
            Log::error("DatabaseBackup: Failed to export {$table} — " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Get the list of tables to back up.
     */
    protected function getTables(): array
    {
        if (!empty($this->tables)) {
            // Filter to only tables that actually exist
            $existing = collect(DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'"))
                ->pluck('tablename')
                ->toArray();

            return array_values(array_intersect($this->tables, $existing));
        }

        // Auto-detect: all public tables except Laravel system tables
        $skip = ['migrations', 'password_reset_tokens', 'personal_access_tokens', 'failed_jobs', 'jobs', 'job_batches', 'cache', 'cache_locks', 'sessions'];

        return collect(DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'"))
            ->pluck('tablename')
            ->reject(fn ($t) => in_array($t, $skip))
            ->values()
            ->toArray();
    }

    /**
     * Write a human-readable log file for the backup run.
     */
    protected function writeLogFile(string $dir, array $summary): void
    {
        $lines = [];
        $lines[] = '=== Database Backup Log ===';
        $lines[] = 'Started:  ' . $summary['started_at'];
        $lines[] = 'Finished: ' . $summary['finished_at'];
        $lines[] = 'Status:   ' . strtoupper($summary['status']);
        $lines[] = 'Tables:   ' . $summary['total_tables'];
        $lines[] = 'Rows:     ' . number_format($summary['total_rows']);
        $lines[] = '';
        $lines[] = '--- Per-table breakdown ---';

        foreach ($summary['tables'] as $table => $info) {
            $status = strtoupper($info['status']);
            $rows = number_format($info['rows']);
            $line = "  {$table}: {$rows} rows [{$status}]";
            if ($info['error']) {
                $line .= " ERROR: {$info['error']}";
            }
            $lines[] = $line;
        }

        if (!empty($summary['errors'])) {
            $lines[] = '';
            $lines[] = '--- Errors ---';
            foreach ($summary['errors'] as $err) {
                $lines[] = "  - {$err}";
            }
        }

        $lines[] = '';
        $lines[] = '=== End of Log ===';

        File::put($dir . DIRECTORY_SEPARATOR . 'backup-log.txt', implode("\n", $lines));
    }

    /**
     * Delete backup folders older than the retention period.
     */
    protected function cleanOldBackups(): void
    {
        if (!File::isDirectory($this->basePath)) {
            return;
        }

        $cutoff = Carbon::now($this->timezone)->subMonths($this->retentionMonths)->format('Y-m');

        foreach (File::directories($this->basePath) as $dir) {
            $folderName = basename($dir);
            // Only consider folders matching YYYY-MM pattern
            if (preg_match('/^\d{4}-\d{2}$/', $folderName) && $folderName < $cutoff) {
                File::deleteDirectory($dir);
                Log::info("DatabaseBackup: Retention cleanup — deleted {$folderName}");
            }
        }
    }

    /**
     * List all existing backup folders with their summaries.
     */
    public function listBackups(): array
    {
        $backups = [];

        if (!File::isDirectory($this->basePath)) {
            return $backups;
        }

        $dirs = File::directories($this->basePath);
        rsort($dirs); // newest first

        foreach ($dirs as $dir) {
            $folder = basename($dir);
            if (!preg_match('/^\d{4}-\d{2}$/', $folder)) {
                continue;
            }

            $summaryFile = $dir . DIRECTORY_SEPARATOR . 'backup-summary.json';
            if (File::exists($summaryFile)) {
                $data = json_decode(File::get($summaryFile), true);
                $backups[] = $data;
            } else {
                // Folder exists but no summary — incomplete
                $files = File::files($dir);
                $backups[] = [
                    'folder' => $folder,
                    'full_path' => $dir,
                    'status' => 'unknown',
                    'total_tables' => count(array_filter($files, fn ($f) => str_ends_with($f, '.xlsx'))),
                    'total_rows' => 0,
                    'started_at' => null,
                    'finished_at' => null,
                    'errors' => [],
                    'tables' => [],
                ];
            }
        }

        return $backups;
    }
}

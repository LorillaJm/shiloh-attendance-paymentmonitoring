<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Illuminate\Console\Command;

class BackupDatabase extends Command
{
    protected $signature = 'backup:run {--force : Skip the lock check}';

    protected $description = 'Export all database tables to Excel files in the data backup folder';

    public function handle(DatabaseBackupService $service): int
    {
        $this->info('Starting database backup...');
        $this->newLine();

        $summary = $service->run();

        // Display results
        $this->info("Status:  {$summary['status']}");
        $this->info("Folder:  {$summary['folder']}");
        $this->info("Tables:  {$summary['total_tables']}");
        $this->info("Rows:    " . number_format($summary['total_rows']));
        $this->info("Started: {$summary['started_at']}");
        $this->info("Ended:   {$summary['finished_at']}");

        if (!empty($summary['tables'])) {
            $this->newLine();
            $this->table(
                ['Table', 'Rows', 'Status'],
                collect($summary['tables'])->map(fn ($info, $table) => [
                    $table,
                    number_format($info['rows']),
                    strtoupper($info['status']),
                ])->toArray()
            );
        }

        if (!empty($summary['errors'])) {
            $this->newLine();
            $this->error('Errors:');
            foreach ($summary['errors'] as $err) {
                $this->warn("  - {$err}");
            }
        }

        return $summary['status'] === 'success' ? self::SUCCESS : self::FAILURE;
    }
}

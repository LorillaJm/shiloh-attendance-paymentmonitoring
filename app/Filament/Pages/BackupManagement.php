<?php

namespace App\Filament\Pages;

use App\Services\DatabaseBackupService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;

class BackupManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-circle-stack';
    protected static string $view = 'filament.pages.backup-management';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?string $title = 'Backup Management';
    protected static ?int $navigationSort = 2;

    public bool $isRunning = false;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperadmin() || $user->isAdmin());
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperadmin() || $user->isAdmin());
    }

    /**
     * Run a backup now (manual trigger).
     */
    public function runBackupNow(): void
    {
        $this->isRunning = true;

        try {
            $service = app(DatabaseBackupService::class);
            $summary = $service->run();

            if ($summary['status'] === 'skipped') {
                Notification::make()
                    ->warning()
                    ->title('Backup Skipped')
                    ->body('Another backup is already running. Please wait.')
                    ->send();
                return;
            }

            if ($summary['status'] === 'failed') {
                Notification::make()
                    ->danger()
                    ->title('Backup Failed')
                    ->body(implode('; ', $summary['errors']))
                    ->persistent()
                    ->send();
                return;
            }

            $msg = "{$summary['total_tables']} tables exported, "
                . number_format($summary['total_rows']) . " total rows. "
                . "Saved to: {$summary['folder']}";

            Notification::make()
                ->success()
                ->title('Backup Completed')
                ->body($msg)
                ->send();

        } catch (\Throwable $e) {
            Notification::make()
                ->danger()
                ->title('Backup Error')
                ->body($e->getMessage())
                ->persistent()
                ->send();
        } finally {
            $this->isRunning = false;
        }
    }

    /**
     * Delete a specific backup folder.
     */
    public function deleteBackup(string $folder): void
    {
        $basePath = base_path(config('backup.path', 'data backup'));
        $target = $basePath . DIRECTORY_SEPARATOR . $folder;

        // Validate folder name to prevent path traversal
        if (!preg_match('/^\d{4}-\d{2}$/', $folder) || !File::isDirectory($target)) {
            Notification::make()->danger()->title('Invalid backup folder.')->send();
            return;
        }

        File::deleteDirectory($target);

        Notification::make()
            ->success()
            ->title("Backup {$folder} deleted.")
            ->send();
    }

    /**
     * Get list of backups for the view.
     */
    public function getBackupsProperty(): array
    {
        return app(DatabaseBackupService::class)->listBackups();
    }

    /**
     * Get config values for the view.
     */
    public function getBackupConfigProperty(): array
    {
        return [
            'path' => config('backup.path', 'data backup'),
            'retention_months' => config('backup.retention_months', 12),
            'tables_count' => count(config('backup.tables', [])),
            'schedule' => '1st of every month at 02:00 AM (Asia/Manila)',
        ];
    }
}

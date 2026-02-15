<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupManagement extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static string $view = 'filament.pages.backup-management';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        // Only show to admins
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canAccess(): bool
    {
        // Only admins can access
        return auth()->user()?->isAdmin() ?? false;
    }

    public function generateBackup(): void
    {
        try {
            $timestamp = now()->format('Y-m-d_His');
            $filename = "backup_{$timestamp}.sql";
            
            // Get database credentials
            $host = config('database.connections.pgsql.host');
            $port = config('database.connections.pgsql.port');
            $database = config('database.connections.pgsql.database');
            $username = config('database.connections.pgsql.username');
            
            // Create backup directory if it doesn't exist
            $backupPath = storage_path('app/backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            $fullPath = $backupPath . '/' . $filename;
            
            // Generate pg_dump command
            $command = sprintf(
                'pg_dump -h %s -p %s -U %s -d %s -F p -f %s',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($database),
                escapeshellarg($fullPath)
            );
            
            Notification::make()
                ->warning()
                ->title('Manual Backup Required')
                ->body("Please run this command in your terminal:\n\n{$command}\n\nOr use Supabase dashboard to create a backup.")
                ->persistent()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Backup Failed')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function getBackupInstructions(): array
    {
        return [
            'manual' => [
                'title' => 'Manual Database Backup',
                'steps' => [
                    '1. Open your terminal/command prompt',
                    '2. Run: pg_dump -h [HOST] -U [USER] -d [DATABASE] > backup.sql',
                    '3. Store the backup file securely',
                ],
            ],
            'supabase' => [
                'title' => 'Supabase Backup',
                'steps' => [
                    '1. Go to your Supabase project dashboard',
                    '2. Navigate to Database > Backups',
                    '3. Click "Create Backup" or enable automatic backups',
                    '4. Download backups as needed',
                ],
            ],
            'automated' => [
                'title' => 'Automated Backup (Recommended)',
                'steps' => [
                    '1. Set up a cron job or scheduled task',
                    '2. Use pg_dump with credentials from .env',
                    '3. Store backups in secure cloud storage (S3, Google Drive, etc.)',
                    '4. Implement backup rotation (keep last 7 days, 4 weeks, 12 months)',
                ],
            ],
        ];
    }
}

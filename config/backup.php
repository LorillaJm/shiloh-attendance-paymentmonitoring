<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Storage Path
    |--------------------------------------------------------------------------
    |
    | Base directory for all backup files. Relative to the project root.
    | Each backup run creates a subfolder like: data backup/2026-04
    |
    */
    'path' => env('BACKUP_PATH', 'data backup'),

    /*
    |--------------------------------------------------------------------------
    | Tables to Export
    |--------------------------------------------------------------------------
    |
    | List of database tables to include in the backup.
    | Set to null or empty to auto-detect all non-system tables.
    |
    */
    'tables' => [
        'users',
        'students',
        'guardians',
        'programs',
        'packages',
        'enrollments',
        'payment_schedules',
        'payment_transactions',
        'attendance_records',
        'session_types',
        'session_occurrences',
        'student_schedules',
        'activity_log',
        'announcements',
        'notifications',
        'system_settings',
    ],

    /*
    |--------------------------------------------------------------------------
    | Retention Policy
    |--------------------------------------------------------------------------
    |
    | How many monthly backup folders to keep. Older folders are deleted
    | automatically after a successful backup run.
    | Set to 0 to disable automatic cleanup.
    |
    */
    'retention_months' => env('BACKUP_RETENTION_MONTHS', 12),

    /*
    |--------------------------------------------------------------------------
    | Chunk Size
    |--------------------------------------------------------------------------
    |
    | Number of rows fetched per chunk when exporting large tables.
    | Keeps memory usage low for tables with many rows.
    |
    */
    'chunk_size' => env('BACKUP_CHUNK_SIZE', 1000),

    /*
    |--------------------------------------------------------------------------
    | Timezone
    |--------------------------------------------------------------------------
    |
    | Timezone used for folder names and log timestamps.
    |
    */
    'timezone' => env('BACKUP_TIMEZONE', 'Asia/Manila'),

];

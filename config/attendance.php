<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Edit Window Days
    |--------------------------------------------------------------------------
    |
    | Number of days after attendance date that records can be edited.
    | Set to null to allow editing anytime.
    | Default: 7 days
    |
    */
    'edit_window_days' => env('ATTENDANCE_EDIT_WINDOW_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Default Status
    |--------------------------------------------------------------------------
    |
    | Default attendance status when creating new records.
    |
    */
    'default_status' => 'PRESENT',

    /*
    |--------------------------------------------------------------------------
    | Status Options
    |--------------------------------------------------------------------------
    |
    | Available attendance status options.
    |
    */
    'status_options' => [
        'PRESENT' => 'Present',
        'ABSENT' => 'Absent',
        'LATE' => 'Late',
        'EXCUSED' => 'Excused',
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Colors
    |--------------------------------------------------------------------------
    |
    | Badge colors for each status in Filament.
    |
    */
    'status_colors' => [
        'PRESENT' => 'success',
        'ABSENT' => 'danger',
        'LATE' => 'warning',
        'EXCUSED' => 'info',
    ],
];

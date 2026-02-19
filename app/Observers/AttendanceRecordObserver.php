<?php

namespace App\Observers;

use App\Models\AttendanceRecord;

class AttendanceRecordObserver
{
    /**
     * Handle the AttendanceRecord "created" event.
     */
    public function created(AttendanceRecord $attendanceRecord): void
    {
        \App\Services\DashboardCacheService::clearAttendanceCaches();
    }

    /**
     * Handle the AttendanceRecord "updated" event.
     */
    public function updated(AttendanceRecord $attendanceRecord): void
    {
        \App\Services\DashboardCacheService::clearAttendanceCaches();
    }

    /**
     * Handle the AttendanceRecord "deleted" event.
     */
    public function deleted(AttendanceRecord $attendanceRecord): void
    {
        \App\Services\DashboardCacheService::clearAttendanceCaches();
    }

    /**
     * Handle the AttendanceRecord "restored" event.
     */
    public function restored(AttendanceRecord $attendanceRecord): void
    {
        //
    }

    /**
     * Handle the AttendanceRecord "force deleted" event.
     */
    public function forceDeleted(AttendanceRecord $attendanceRecord): void
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Models\AttendanceRecord;
use App\Models\Enrollment;

class AttendanceRecordObserver
{
    /**
     * Handle the AttendanceRecord "created" event.
     */
    public function created(AttendanceRecord $attendanceRecord): void
    {
        \App\Services\DashboardCacheService::clearAttendanceCaches();
        
        // Increment session count if status is PRESENT
        if ($attendanceRecord->status === 'PRESENT') {
            $this->incrementSessionCount($attendanceRecord);
        }
    }

    /**
     * Handle the AttendanceRecord "updated" event.
     */
    public function updated(AttendanceRecord $attendanceRecord): void
    {
        \App\Services\DashboardCacheService::clearAttendanceCaches();
        
        // Check if status changed
        if ($attendanceRecord->wasChanged('status')) {
            $oldStatus = $attendanceRecord->getOriginal('status');
            $newStatus = $attendanceRecord->status;
            
            // If changed from PRESENT to something else, decrement
            if ($oldStatus === 'PRESENT' && $newStatus !== 'PRESENT') {
                $this->decrementSessionCount($attendanceRecord);
            }
            
            // If changed to PRESENT from something else, increment
            if ($oldStatus !== 'PRESENT' && $newStatus === 'PRESENT') {
                $this->incrementSessionCount($attendanceRecord);
            }
        }
    }

    /**
     * Handle the AttendanceRecord "deleted" event.
     */
    public function deleted(AttendanceRecord $attendanceRecord): void
    {
        \App\Services\DashboardCacheService::clearAttendanceCaches();
        
        // Decrement session count if status was PRESENT
        if ($attendanceRecord->status === 'PRESENT') {
            $this->decrementSessionCount($attendanceRecord);
        }
    }

    /**
     * Handle the AttendanceRecord "restored" event.
     */
    public function restored(AttendanceRecord $attendanceRecord): void
    {
        // Increment session count if status is PRESENT
        if ($attendanceRecord->status === 'PRESENT') {
            $this->incrementSessionCount($attendanceRecord);
        }
    }

    /**
     * Handle the AttendanceRecord "force deleted" event.
     */
    public function forceDeleted(AttendanceRecord $attendanceRecord): void
    {
        // Decrement session count if status was PRESENT
        if ($attendanceRecord->status === 'PRESENT') {
            $this->decrementSessionCount($attendanceRecord);
        }
    }

    /**
     * Increment session count for the student's active enrollment.
     */
    protected function incrementSessionCount(AttendanceRecord $attendanceRecord): void
    {
        $enrollment = Enrollment::where('student_id', $attendanceRecord->student_id)
            ->where('status', 'ACTIVE')
            ->first();
            
        if ($enrollment && $enrollment->total_sessions > 0) {
            $enrollment->incrementSessionsUsed();
        }
    }

    /**
     * Decrement session count for the student's active enrollment.
     */
    protected function decrementSessionCount(AttendanceRecord $attendanceRecord): void
    {
        $enrollment = Enrollment::where('student_id', $attendanceRecord->student_id)
            ->where('status', 'ACTIVE')
            ->first();
            
        if ($enrollment) {
            $enrollment->decrementSessionsUsed();
        }
    }
}

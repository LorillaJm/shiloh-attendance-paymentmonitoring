<?php

namespace App\Observers;

use App\Models\AttendanceRecord;
use App\Models\Enrollment;
use App\Models\SessionOccurrence;

class AttendanceRecordObserver
{
    /**
     * Handle the AttendanceRecord "created" event.
     */
    public function created(AttendanceRecord $attendanceRecord): void
    {
        \App\Services\DashboardCacheService::clearAttendanceSummary();
        
        // Increment session count if status is PRESENT
        if ($attendanceRecord->status === 'PRESENT') {
            $this->incrementSessionCount($attendanceRecord);
        }

        // Sync session occurrence status
        $this->syncSessionOccurrenceStatus($attendanceRecord);
    }

    /**
     * Handle the AttendanceRecord "updated" event.
     */
    public function updated(AttendanceRecord $attendanceRecord): void
    {
        \App\Services\DashboardCacheService::clearAttendanceSummary();
        
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

            // Sync session occurrence status on status change
            $this->syncSessionOccurrenceStatus($attendanceRecord);
        }
    }

    /**
     * Handle the AttendanceRecord "deleted" event.
     */
    public function deleted(AttendanceRecord $attendanceRecord): void
    {
        \App\Services\DashboardCacheService::clearAttendanceSummary();
        
        // Decrement session count if status was PRESENT
        if ($attendanceRecord->status === 'PRESENT') {
            $this->decrementSessionCount($attendanceRecord);
        }

        // Revert session occurrence back to SCHEDULED when attendance is deleted
        $this->revertSessionOccurrenceStatus($attendanceRecord);
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

    /**
     * Sync the related session occurrence status based on attendance status.
     * When PRESENT → mark session occurrence as COMPLETED.
     * When not PRESENT → revert session occurrence to SCHEDULED (if it was auto-completed).
     */
    protected function syncSessionOccurrenceStatus(AttendanceRecord $attendanceRecord): void
    {
        $occurrences = SessionOccurrence::where('student_id', $attendanceRecord->student_id)
            ->whereDate('session_date', $attendanceRecord->attendance_date)
            ->get();

        if ($occurrences->isEmpty()) {
            return;
        }

        foreach ($occurrences as $occurrence) {
            if ($attendanceRecord->status === 'PRESENT') {
                // Only update if not already COMPLETED to avoid unnecessary writes
                if ($occurrence->status !== 'COMPLETED') {
                    $occurrence->update(['status' => 'COMPLETED']);
                }

                // Link the attendance record to this session occurrence if not already linked
                if (! $attendanceRecord->session_occurrence_id) {
                    $attendanceRecord->updateQuietly(['session_occurrence_id' => $occurrence->id]);
                }
            } else {
                // Revert to SCHEDULED only if it was COMPLETED (avoid overwriting CANCELLED)
                if ($occurrence->status === 'COMPLETED') {
                    $occurrence->update(['status' => 'SCHEDULED']);
                }
            }
        }
    }

    /**
     * Revert session occurrence status when an attendance record is deleted.
     */
    protected function revertSessionOccurrenceStatus(AttendanceRecord $attendanceRecord): void
    {
        // If the record was linked to a specific session occurrence
        if ($attendanceRecord->session_occurrence_id) {
            $occurrence = SessionOccurrence::find($attendanceRecord->session_occurrence_id);
            if ($occurrence && $occurrence->status === 'COMPLETED') {
                $occurrence->update(['status' => 'SCHEDULED']);
            }
            return;
        }

        // Fallback: find by student + date
        SessionOccurrence::where('student_id', $attendanceRecord->student_id)
            ->whereDate('session_date', $attendanceRecord->attendance_date)
            ->where('status', 'COMPLETED')
            ->update(['status' => 'SCHEDULED']);
    }
}

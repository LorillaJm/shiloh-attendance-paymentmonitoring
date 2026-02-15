<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AttendanceRecord;

class AttendanceRecordPolicy
{
    /**
     * Determine if the user can view any attendance records.
     */
    public function viewAny(User $user): bool
    {
        return true; // Both ADMIN and USER can view
    }

    /**
     * Determine if the user can view the attendance record.
     */
    public function view(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return true; // Both ADMIN and USER can view
    }

    /**
     * Determine if the user can create attendance records.
     */
    public function create(User $user): bool
    {
        return true; // Both ADMIN and USER can create
    }

    /**
     * Determine if the user can update the attendance record.
     */
    public function update(User $user, AttendanceRecord $attendanceRecord): bool
    {
        // Admin can edit anything
        if ($user->isAdmin()) {
            return true;
        }

        // Check if within edit window
        if (!$attendanceRecord->canBeEdited()) {
            return false;
        }

        // User can edit their own records
        return $attendanceRecord->encoded_by_user_id === $user->id;
    }

    /**
     * Determine if the user can delete the attendance record.
     */
    public function delete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        // Admin can delete anything
        if ($user->isAdmin()) {
            return true;
        }

        // Check if within edit window
        if (!$attendanceRecord->canBeEdited()) {
            return false;
        }

        // User can delete their own records
        return $attendanceRecord->encoded_by_user_id === $user->id;
    }

    /**
     * Determine if the user can restore the attendance record.
     */
    public function restore(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the attendance record.
     */
    public function forceDelete(User $user, AttendanceRecord $attendanceRecord): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can batch encode attendance.
     */
    public function batchEncode(User $user): bool
    {
        return true; // Both ADMIN and USER can batch encode
    }

    /**
     * Determine if the user can edit attendance for a specific date.
     * This is used by the batch encoder to check edit window.
     */
    public function editDate(User $user, string $attendanceDate): bool
    {
        // Admin can edit any date
        if ($user->isAdmin()) {
            return true;
        }

        // Check if date is within edit window
        $editWindowDays = config('attendance.edit_window_days', 7);
        
        if ($editWindowDays === null) {
            return true; // No restriction
        }

        $daysSince = \Carbon\Carbon::parse($attendanceDate)->diffInDays(now(), false);
        
        return $daysSince <= $editWindowDays;
    }
}

<?php

namespace App\Policies;

use App\Models\StudentSchedule;
use App\Models\User;

class StudentSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        // Only ADMIN can view student schedules
        return $user->isAdmin();
    }

    public function view(User $user, StudentSchedule $studentSchedule): bool
    {
        // Only ADMIN can view student schedules
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        // Only ADMIN can create student schedules
        return $user->isAdmin();
    }

    public function update(User $user, StudentSchedule $studentSchedule): bool
    {
        // Only ADMIN can update student schedules
        return $user->isAdmin();
    }

    public function delete(User $user, StudentSchedule $studentSchedule): bool
    {
        // Only ADMIN can delete student schedules
        return $user->isAdmin();
    }
}

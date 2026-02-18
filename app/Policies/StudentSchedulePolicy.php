<?php

namespace App\Policies;

use App\Models\StudentSchedule;
use App\Models\User;

class StudentSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, StudentSchedule $studentSchedule): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $studentSchedule->teacher_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, StudentSchedule $studentSchedule): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, StudentSchedule $studentSchedule): bool
    {
        return $user->isAdmin();
    }
}

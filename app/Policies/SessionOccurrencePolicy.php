<?php

namespace App\Policies;

use App\Models\SessionOccurrence;
use App\Models\User;

class SessionOccurrencePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function view(User $user, SessionOccurrence $sessionOccurrence): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $sessionOccurrence->teacher_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function update(User $user, SessionOccurrence $sessionOccurrence): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $sessionOccurrence->teacher_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, SessionOccurrence $sessionOccurrence): bool
    {
        return $user->isAdmin();
    }
}

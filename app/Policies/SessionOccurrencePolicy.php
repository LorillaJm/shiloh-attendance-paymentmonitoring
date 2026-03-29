<?php

namespace App\Policies;

use App\Models\SessionOccurrence;
use App\Models\User;

class SessionOccurrencePolicy
{
    public function viewAny(User $user): bool
    {
        // Only ADMIN can view session occurrences
        return $user->isAdmin();
    }

    public function view(User $user, SessionOccurrence $sessionOccurrence): bool
    {
        // Only ADMIN can view session occurrences
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        // Only ADMIN can create session occurrences
        return $user->isAdmin();
    }

    public function update(User $user, SessionOccurrence $sessionOccurrence): bool
    {
        // Only ADMIN can update session occurrences
        return $user->isAdmin();
    }

    public function delete(User $user, SessionOccurrence $sessionOccurrence): bool
    {
        // Only ADMIN can delete session occurrences
        return $user->isAdmin();
    }
}

<?php

namespace App\Policies;

use App\Models\Guardian;
use App\Models\User;

class GuardianPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Guardian $guardian): bool
    {
        return $user->isAdmin() || $user->id === $guardian->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Guardian $guardian): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Guardian $guardian): bool
    {
        return $user->isAdmin();
    }
}

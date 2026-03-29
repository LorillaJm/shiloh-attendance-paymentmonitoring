<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     * Only SUPERADMIN can view user list.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Superadmin can view anyone
        if ($user->isSuperadmin()) {
            return true;
        }
        
        // Users can view themselves
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     * Only SUPERADMIN can create users.
     */
    public function create(User $user): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can update the model.
     * SUPERADMIN can update anyone, users can update themselves.
     */
    public function update(User $user, User $model): bool
    {
        // Superadmin can update anyone
        if ($user->isSuperadmin()) {
            return true;
        }
        
        // Users can update themselves (profile)
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     * Only SUPERADMIN can delete users (except themselves).
     */
    public function delete(User $user, User $model): bool
    {
        // Only superadmin can delete users
        // Cannot delete yourself
        return $user->isSuperadmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->isSuperadmin() && $user->id !== $model->id;
    }

    /**
     * Determine whether the user can assign roles to the model.
     * Only SUPERADMIN can assign roles (except to themselves).
     */
    public function assignRole(User $user, User $model): bool
    {
        // Only superadmin can assign roles
        if (!$user->isSuperadmin()) {
            return false;
        }
        
        // Cannot change your own role
        return $user->id !== $model->id;
    }
}

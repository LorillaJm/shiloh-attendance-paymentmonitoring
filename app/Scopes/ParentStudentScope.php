<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ParentStudentScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();
        
        // Skip scope for SUPERADMIN and ADMIN
        if (!$user || $user->isSuperadmin() || $user->isAdmin()) {
            return;
        }
        
        // Only apply scope for parent users
        if ($user->isParent()) {
            if ($user->guardian) {
                $builder->whereHas('guardians', function ($query) use ($user) {
                    $query->where('guardians.id', $user->guardian->id);
                });
            } else {
                // Parent has no guardian relationship - no access
                $builder->whereRaw('1 = 0');
            }
        }
    }
}

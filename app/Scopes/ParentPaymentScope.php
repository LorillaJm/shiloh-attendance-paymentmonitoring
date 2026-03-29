<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class ParentPaymentScope implements Scope
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
        
        // For PARENT: filter to only payment records of their children
        if ($user->isParent()) {
            if ($user->guardian) {
                $studentIds = $user->guardian->students()->pluck('students.id');
                $builder->whereHas('enrollment', function ($query) use ($studentIds) {
                    $query->whereIn('student_id', $studentIds);
                });
            } else {
                // Parent has no guardian relationship - no access
                $builder->whereRaw('1 = 0');
            }
        }
    }
}

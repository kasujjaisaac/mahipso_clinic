<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Auth\Access\Response;

class VisitPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canAccessAnyModule(['front_office', 'clinic', 'nursing']);
    }

    public function view(User $user, Visit $visit): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->branch_id === $visit->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->canAccessAnyModule(['front_office', 'clinic', 'nursing']);
    }

    public function update(User $user, Visit $visit): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->canAccessAnyModule(['front_office', 'clinic', 'nursing']) && $user->branch_id === $visit->branch_id;
    }

    public function delete(User $user, Visit $visit): bool
    {
        return $user->hasRole('super_admin') || ($user->hasRole('branch_admin') && $user->branch_id === $visit->branch_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Visit $visit): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Visit $visit): bool
    {
        return false;
    }
}

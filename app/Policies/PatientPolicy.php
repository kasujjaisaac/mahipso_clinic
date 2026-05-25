<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessAnyModule(['front_office', 'clinic', 'nursing']);
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->isSuperAdmin() || $user->branch_id === $patient->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->canAccessAnyModule(['front_office', 'clinic', 'nursing']);
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->isSuperAdmin()
            || ($user->canAccessAnyModule(['front_office', 'clinic', 'nursing']) && $user->branch_id === $patient->branch_id);
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->isSuperAdmin() || ($user->hasRole('branch_admin') && $user->branch_id === $patient->branch_id);
    }
}

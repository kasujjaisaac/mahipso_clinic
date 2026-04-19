<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'branch_admin', 'doctor', 'nurse', 'receptionist']);
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->isSuperAdmin() || $user->branch_id === $patient->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'branch_admin', 'doctor', 'nurse', 'receptionist']);
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->isSuperAdmin()
            || ($user->hasRole(['branch_admin', 'doctor', 'nurse', 'receptionist']) && $user->branch_id === $patient->branch_id);
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->isSuperAdmin() || ($user->hasRole('branch_admin') && $user->branch_id === $patient->branch_id);
    }
}

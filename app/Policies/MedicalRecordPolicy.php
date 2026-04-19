<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicalRecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin','branch_admin','doctor','nurse']);
    }

    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->branch_id === $medicalRecord->visit->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin','branch_admin','doctor','nurse']);
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->hasRole(['branch_admin','doctor','nurse']) && $user->branch_id === $medicalRecord->visit->branch_id;
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->hasRole('super_admin') || ($user->hasRole('branch_admin') && $user->branch_id === $medicalRecord->visit->branch_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalRecord $medicalRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalRecord $medicalRecord): bool
    {
        return false;
    }
}

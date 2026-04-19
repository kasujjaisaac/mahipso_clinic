<?php

namespace App\Policies;

use App\Models\HivRecord;
use App\Models\User;

class HivRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin','branch_admin','doctor','nurse']);
    }

    public function view(User $user, HivRecord $hivRecord): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->branch_id === $hivRecord->visit->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin','branch_admin','doctor','nurse']);
    }

    public function update(User $user, HivRecord $hivRecord): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->hasRole(['branch_admin','doctor','nurse']) && $user->branch_id === $hivRecord->visit->branch_id;
    }

    public function delete(User $user, HivRecord $hivRecord): bool
    {
        return $user->hasRole('super_admin') || ($user->hasRole('branch_admin') && $user->branch_id === $hivRecord->visit->branch_id);
    }

    public function restore(User $user, HivRecord $hivRecord): bool
    {
        return false;
    }

    public function forceDelete(User $user, HivRecord $hivRecord): bool
    {
        return false;
    }
}

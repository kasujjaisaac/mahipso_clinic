<?php

namespace App\Policies;

use App\Models\Pharmacy;
use App\Models\User;

class PharmacyPolicy
{
    public function viewAny(User $user)
    {
        return $user->canAccessModule('pharmacy');
    }

    public function view(User $user, Pharmacy $pharmacy)
    {
        return $user->isSuperAdmin()
            || ($user->canAccessModule('pharmacy') && $user->branch_id === $pharmacy->branch_id);
    }

    public function update(User $user, Pharmacy $pharmacy)
    {
        return $user->isSuperAdmin()
            || ($user->canAccessModule('pharmacy') && $user->branch_id === $pharmacy->branch_id);
    }
}

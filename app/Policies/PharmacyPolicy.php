<?php

namespace App\Policies;

use App\Models\Pharmacy;
use App\Models\User;

class PharmacyPolicy
{
    public function viewAny(User $user)
    {
        // Allow super_admin, branch_admin, and nurses
        return $user->hasRole(['super_admin', 'branch_admin', 'nurse']);
    }

    public function view(User $user, Pharmacy $pharmacy)
    {
        // Super admin can view all, branch admin/nurse can view their branch
        return $user->hasRole('super_admin') || ($user->branch_id === $pharmacy->branch_id);
    }

    public function update(User $user, Pharmacy $pharmacy)
    {
        // Super admin can update all, branch admin/nurse can update their branch
        return $user->hasRole('super_admin') || ($user->branch_id === $pharmacy->branch_id);
    }
}

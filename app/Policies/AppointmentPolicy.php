<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin','branch_admin','receptionist','doctor']);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->branch_id === $appointment->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin','branch_admin','receptionist','doctor']);
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->hasRole(['branch_admin', 'receptionist', 'doctor'])
            && $user->branch_id === $appointment->branch_id;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        return $user->hasRole('branch_admin') && $user->branch_id === $appointment->branch_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        return false;
    }
}

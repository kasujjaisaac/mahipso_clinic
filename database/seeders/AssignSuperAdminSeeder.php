<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignSuperAdminSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        if ($user && !$user->hasRole('super_admin')) {
            // Ensure the role exists
            $role = Role::firstOrCreate(['name' => 'super_admin']);
            $user->assignRole($role);
            $this->command->info('Assigned super_admin role to user: ' . $user->email);
        }
    }
}

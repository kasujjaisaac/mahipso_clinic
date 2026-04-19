<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $branches = [
            ['code'=>'MASAKA','name'=>'Masaka Main Clinic','status'=>'active','address'=>'Masaka Town'],
            ['code'=>'BUKAKATA','name'=>'Bukakata Clinic','status'=>'active','address'=>'Bukakata Sub-county'],
            ['code'=>'KALANGALA','name'=>'Kalangala Clinic','status'=>'active','address'=>'Kalangala Island'],
        ];

        foreach ($branches as $branchData) {
            Branch::firstOrCreate(['code'=>$branchData['code']], $branchData);
        }

        $branch = Branch::where('code','MASAKA')->first();

        $roles = ['super_admin', 'branch_admin', 'doctor', 'nurse', 'receptionist', 'counselor', 'pharmacist', 'labtech', 'patient'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $branchAdmin = User::firstOrCreate(
            ['email' => 'admin@clinic.test'],
            [
                'name' => 'Masaka Branch Admin',
                'password' => Hash::make('password'),
                'branch_id' => $branch->id,
            ]
        );

        $branchAdmin->syncRoles(['branch_admin']);

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@clinic.test'],
            [
                'name' => 'MAHIPSO Super Admin',
                'password' => Hash::make('password'),
                'branch_id' => null,
            ]
        );

        $superAdmin->syncRoles(['super_admin']);

        // Ensure at least one user has the super_admin role
        $this->call(AssignSuperAdminSeeder::class);

        // Seed pharmacies, products, and sales
        $this->call(PharmacySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(SaleSeeder::class);
    }
}

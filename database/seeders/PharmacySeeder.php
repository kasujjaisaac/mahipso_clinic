<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pharmacy;
use App\Models\Branch;

class PharmacySeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        foreach ($branches as $branch) {
            Pharmacy::firstOrCreate([
                'branch_id' => $branch->id,
            ]);
        }
    }
}

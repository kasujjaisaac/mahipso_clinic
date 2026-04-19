<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

trait ResolvesBranchContext
{
    protected function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }

    protected function branchFilterId(?Request $request = null): ?int
    {
        $user = $this->currentUser();

        if (! $user->isSuperAdmin()) {
            return $user->branch_id;
        }

        $requestedBranchId = (int) ($request?->query('branch') ?? 0);

        if ($requestedBranchId > 0 && Branch::whereKey($requestedBranchId)->exists()) {
            return $requestedBranchId;
        }

        return null;
    }

    protected function selectedBranch(?Request $request = null): ?Branch
    {
        $user = $this->currentUser();

        if (! $user->isSuperAdmin()) {
            return $user->branch;
        }

        $requestedBranchId = (int) ($request?->query('branch') ?? 0);

        if ($requestedBranchId > 0) {
            return Branch::find($requestedBranchId);
        }

        return Branch::active()->orderBy('name')->first();
    }

    protected function selectedBranchId(?Request $request = null): ?int
    {
        return $this->selectedBranch($request)?->id;
    }
}

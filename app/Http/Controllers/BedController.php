<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

            return $next($request);
        });
    }

    public function store(Request $request, Ward $ward)
    {
        $this->guardWard($ward);

        $validated = $request->validate([
            'bed_number' => ['required', 'string', 'max:50', Rule::unique('beds')->where('ward_id', $ward->id)],
            'status' => 'required|in:available,reserved,cleaning,maintenance',
            'notes' => 'nullable|string|max:1000',
        ]);

        $ward->beds()->create($validated);

        return back()->with('success', 'Bed added.');
    }

    public function update(Request $request, Bed $bed)
    {
        $bed->load('ward');
        $this->guardWard($bed->ward);
        abort_if($bed->currentAdmission()->exists() && $request->input('status') !== Bed::STATUS_OCCUPIED, 422, 'Occupied beds cannot be manually changed until the admission moves or discharges.');

        $validated = $request->validate([
            'status' => 'required|in:available,reserved,cleaning,maintenance,occupied',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validated['status'] === Bed::STATUS_AVAILABLE) {
            $validated['last_cleaned_at'] = now();
        }

        $bed->update($validated);

        return back()->with('success', 'Bed updated.');
    }

    private function guardWard(Ward $ward): void
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->branch_id === $ward->branch_id, 404);
    }
}

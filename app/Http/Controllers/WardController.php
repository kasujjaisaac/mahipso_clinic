<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Bed;
use App\Models\Branch;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WardController extends Controller
{
    use ResolvesBranchContext;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $branchId = $this->branchFilterId($request);
        $wards = Ward::with(['branch', 'beds.currentAdmission.patient'])
            ->visibleTo($request->user(), $branchId)
            ->orderBy('name')
            ->get();

        $activeAdmissions = $wards->flatMap->beds->filter(fn ($bed) => $bed->status === Bed::STATUS_OCCUPIED)->count();
        $availableBeds = $wards->flatMap->beds->where('status', Bed::STATUS_AVAILABLE)->count();

        return view('wards.index', compact('wards', 'activeAdmissions', 'availableBeds'));
    }

    public function create()
    {
        abort_unless(auth()->user()->canAccessModule('inpatient_ward'), 403);
        $branches = auth()->user()->isSuperAdmin() ? Branch::active()->orderBy('name')->get() : Branch::whereKey(auth()->user()->branch_id)->get();

        return view('wards.create', compact('branches'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

        $validated = $request->validate([
            'branch_id' => ['required', Rule::exists('branches', 'id')->where('status', 'active')],
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:medical,surgical,maternity,pediatric,icu,isolation,observation,other',
            'gender_restriction' => 'required|in:none,male,female',
            'is_active' => 'nullable|boolean',
            'bed_count' => 'nullable|integer|min:0|max:200',
        ]);

        if (! $request->user()->isSuperAdmin()) {
            $validated['branch_id'] = $request->user()->branch_id;
        }

        $bedCount = (int) ($validated['bed_count'] ?? 0);
        unset($validated['bed_count']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $ward = Ward::create($validated);

        for ($i = 1; $i <= $bedCount; $i++) {
            $ward->beds()->create(['bed_number' => (string) $i, 'status' => Bed::STATUS_AVAILABLE]);
        }

        return redirect()->route('wards.show', $ward)->with('success', 'Ward created.');
    }

    public function show(Ward $ward)
    {
        $this->guardWard($ward);
        $ward->load(['branch', 'beds.currentAdmission.patient', 'admissions.patient']);

        return view('wards.show', compact('ward'));
    }

    public function update(Request $request, Ward $ward)
    {
        $this->guardWard($ward);
        abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'type' => 'required|in:medical,surgical,maternity,pediatric,icu,isolation,observation,other',
            'gender_restriction' => 'required|in:none,male,female',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $ward->update($validated);

        return back()->with('success', 'Ward updated.');
    }

    private function guardWard(Ward $ward): void
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->branch_id === $ward->branch_id, 404);
    }
}

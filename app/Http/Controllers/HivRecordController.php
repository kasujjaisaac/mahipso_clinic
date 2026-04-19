<?php

namespace App\Http\Controllers;

use App\Models\HivRecord;
use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HivRecordController extends Controller
{
    use ResolvesBranchContext;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $this->currentUser();
        $branchId = $this->branchFilterId($request);

        $hivRecords = HivRecord::with(['visit', 'patient', 'provider'])
            ->visibleTo($user, $branchId)
            ->when($request->query('search'), function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('mrn', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('hiv_records.index', compact('hivRecords'));
    }

    public function create(Request $request)
    {
        $branchId = $this->selectedBranchId($request);
        $visits = Visit::where('branch_id', $branchId)->where('status', 'open')->get();
        $patients = Patient::where('branch_id', $branchId)->get();
        $providers = User::role(['doctor','nurse'])->where('branch_id', $branchId)->get();

        $visitId = $request->query('visit_id');

        return view('hiv_records.create', compact('visits','patients','providers','visitId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'patient_id' => 'nullable|exists:patients,id',
            'provider_id' => 'nullable|exists:users,id',
            'test_type' => 'required|in:rapid,elisa,pcr,viral_load,cd4,other',
            'test_result' => 'required|in:negative,positive,indeterminate,unknown',
            'cd4_count' => 'nullable|integer|min:0',
            'viral_load' => 'nullable|integer|min:0',
            'art_status' => 'nullable|string|max:255',
            'regimen' => 'nullable|string|max:255',
            'adherence' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $visit = Visit::visibleTo($this->currentUser())->findOrFail($validated['visit_id']);
        $validated['patient_id'] = $validated['patient_id'] ?? $visit->patient_id;

        Patient::visibleTo($this->currentUser())
            ->whereKey($validated['patient_id'])
            ->where('branch_id', $visit->branch_id)
            ->firstOrFail();

        if (! empty($validated['provider_id'])) {
            User::role(['doctor', 'nurse'])
                ->whereKey($validated['provider_id'])
                ->where('branch_id', $visit->branch_id)
                ->firstOrFail();
        }

        $hivRecord = HivRecord::create($validated);

        return redirect()->route('hiv-records.show', $hivRecord)->with('success', 'HIV record saved.');
    }

    public function show(HivRecord $hivRecord)
    {
        $this->authorize('view', $hivRecord);

        return view('hiv_records.show', compact('hivRecord'));
    }

    public function edit(HivRecord $hivRecord)
    {
        $this->authorize('update', $hivRecord);

        $branchId = $hivRecord->visit->branch_id;
        $visits = Visit::where('branch_id', $branchId)->get();
        $patients = Patient::where('branch_id', $branchId)->get();
        $providers = User::role(['doctor','nurse'])->where('branch_id', $branchId)->get();

        return view('hiv_records.edit', compact('hivRecord','visits','patients','providers'));
    }

    public function update(Request $request, HivRecord $hivRecord)
    {
        $this->authorize('update', $hivRecord);

        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'patient_id' => 'nullable|exists:patients,id',
            'provider_id' => 'nullable|exists:users,id',
            'test_type' => 'required|in:rapid,elisa,pcr,viral_load,cd4,other',
            'test_result' => 'required|in:negative,positive,indeterminate,unknown',
            'cd4_count' => 'nullable|integer|min:0',
            'viral_load' => 'nullable|integer|min:0',
            'art_status' => 'nullable|string|max:255',
            'regimen' => 'nullable|string|max:255',
            'adherence' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $visit = Visit::visibleTo($this->currentUser())->findOrFail($validated['visit_id']);
        $validated['patient_id'] = $validated['patient_id'] ?? $visit->patient_id;

        Patient::visibleTo($this->currentUser())
            ->whereKey($validated['patient_id'])
            ->where('branch_id', $visit->branch_id)
            ->firstOrFail();

        if (! empty($validated['provider_id'])) {
            User::role(['doctor', 'nurse'])
                ->whereKey($validated['provider_id'])
                ->where('branch_id', $visit->branch_id)
                ->firstOrFail();
        }

        $hivRecord->update($validated);

        return redirect()->route('hiv-records.show', $hivRecord)->with('success', 'HIV record updated.');
    }

    public function destroy(HivRecord $hivRecord)
    {
        $this->authorize('delete', $hivRecord);
        $hivRecord->delete();

        return redirect()->route('hiv-records.index')->with('success', 'HIV record deleted.');
    }
}

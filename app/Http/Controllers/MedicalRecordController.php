<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    use ResolvesBranchContext;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', MedicalRecord::class);

        $user = $this->currentUser();
        $branchId = $this->branchFilterId($request);

        $medicalRecords = MedicalRecord::with(['patient', 'provider', 'visit'])
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

        return view('medical_records.index', compact('medicalRecords'));
    }

    public function create()
    {
        $this->authorize('create', MedicalRecord::class);

        $branchId = $this->selectedBranchId(request());
        $visits = Visit::where('branch_id', $branchId)->where('status','open')->get();
        $patients = Patient::where('branch_id', $branchId)->get();
        $providers = User::role(['doctor','nurse'])->where('branch_id', $branchId)->get();

        return view('medical_records.create', compact('visits','patients','providers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', MedicalRecord::class);

        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'patient_id' => 'nullable|exists:patients,id',
            'provider_id' => 'nullable|exists:users,id',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'plan' => 'nullable|string',
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

        $medical = MedicalRecord::create($validated);

        return redirect()->route('medical-records.show', $medical)->with('success', 'Medical record saved.');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $this->authorize('view', $medicalRecord);
        return view('medical_records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);

        $branchId = $medicalRecord->visit->branch_id;
        $visits = Visit::where('branch_id', $branchId)->get();
        $patients = Patient::where('branch_id', $branchId)->get();
        $providers = User::role(['doctor','nurse'])->where('branch_id', $branchId)->get();

        return view('medical_records.edit', compact('medicalRecord','visits','patients','providers'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);

        $validated = $request->validate([
            'visit_id' => 'required|exists:visits,id',
            'patient_id' => 'nullable|exists:patients,id',
            'provider_id' => 'nullable|exists:users,id',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'plan' => 'nullable|string',
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

        $medicalRecord->update($validated);

        return redirect()->route('medical-records.show', $medicalRecord)->with('success', 'Medical record updated.');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $this->authorize('delete', $medicalRecord);
        $medicalRecord->delete();

        return redirect()->route('medical-records.index')->with('success', 'Medical record deleted.');
    }
}

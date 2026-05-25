<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Patient;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    use ResolvesBranchContext;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Visit::class);

        $user = $this->currentUser();
        $branchId = $this->branchFilterId($request);

        $visits = Visit::with(['patient', 'provider', 'appointment'])
            ->visibleTo($user, $branchId)
            ->when($request->query('search'), function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('mrn', 'like', "%{$search}%");
                });
            })
            ->orderBy('visit_date', 'desc')
            ->paginate(15);

        return view('visits.index', compact('visits'));
    }

    public function create()
    {
        $this->authorize('create', Visit::class);

        $branchId = $this->selectedBranchId(request());
        $patients = Patient::where('branch_id', $branchId)->orderBy('last_name')->get();
        $providers = User::role(['doctor', 'nurse'])->where('branch_id', $branchId)->get();
        $appointments = Appointment::where('branch_id', $branchId)->where('status', 'scheduled')->orderBy('scheduled_at')->get();

        return view('visits.create', compact('patients','providers','appointments'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Visit::class);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'provider_id' => 'nullable|exists:users,id',
            'visit_date' => 'required|date',
            'visit_type' => 'required|in:general,hiv,counseling,lab,pharmacy,other',
            'chief_complaint' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:open,closed,cancelled',
        ]);

        $patient = Patient::visibleTo($this->currentUser())->findOrFail($validated['patient_id']);

        if (! empty($validated['appointment_id'])) {
            Appointment::visibleTo($this->currentUser())
                ->whereKey($validated['appointment_id'])
                ->where('branch_id', $patient->branch_id)
                ->firstOrFail();
        }

        if (! empty($validated['provider_id'])) {
            User::role(['doctor', 'nurse'])
                ->whereKey($validated['provider_id'])
                ->where('branch_id', $patient->branch_id)
                ->firstOrFail();
        }

        $validated['branch_id'] = $patient->branch_id;
        $validated['workflow_stage'] = Visit::STAGE_CHECKED_IN;
        $validated['checked_in_at'] = now();

        Visit::create($validated);

        return redirect()->route('visits.index')->with('success', 'Visit created.');
    }

    public function show(Visit $visit)
    {
        $this->authorize('view', $visit);
        $visit->load(['medicalRecords', 'patient', 'provider', 'admission']);

        $pharmacyForVisit = Pharmacy::where('branch_id', $visit->branch_id)->first();

        return view('visits.show', compact('visit', 'pharmacyForVisit'));
    }

    public function edit(Visit $visit)
    {
        $this->authorize('update', $visit);

        $branchId = $visit->branch_id;
        $patients = Patient::where('branch_id', $branchId)->orderBy('last_name')->get();
        $providers = User::role(['doctor', 'nurse'])->where('branch_id', $branchId)->get();

        return view('visits.edit', compact('visit','patients','providers'));
    }

    public function update(Request $request, Visit $visit)
    {
        $this->authorize('update', $visit);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'provider_id' => 'nullable|exists:users,id',
            'visit_date' => 'required|date',
            'visit_type' => 'required|in:general,hiv,counseling,lab,pharmacy,other',
            'chief_complaint' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:open,closed,cancelled',
        ]);

        $patient = Patient::visibleTo($this->currentUser())->findOrFail($validated['patient_id']);

        if (! empty($validated['appointment_id'])) {
            Appointment::visibleTo($this->currentUser())
                ->whereKey($validated['appointment_id'])
                ->where('branch_id', $patient->branch_id)
                ->firstOrFail();
        }

        if (! empty($validated['provider_id'])) {
            User::role(['doctor', 'nurse'])
                ->whereKey($validated['provider_id'])
                ->where('branch_id', $patient->branch_id)
                ->firstOrFail();
        }

        $validated['branch_id'] = $patient->branch_id;

        $visit->update($validated);

        return redirect()->route('visits.index')->with('success', 'Visit updated.');
    }

    public function destroy(Visit $visit)
    {
        $this->authorize('delete', $visit);
        $visit->delete();

        return redirect()->route('visits.index')->with('success', 'Visit deleted.');
    }
}

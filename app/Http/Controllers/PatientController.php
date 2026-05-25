<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Branch;
use App\Models\Patient;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Visit;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    use ResolvesBranchContext;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Patient::class);

        $user = $this->currentUser();
        $branchId = $this->branchFilterId($request);

        $query = Patient::query()
            ->with('branch')
            ->visibleTo($user, $branchId);

        // Search filter
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('mrn', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Gender filter
        if ($gender = $request->query('gender')) {
            $query->where('gender', $gender);
        }

        // Date range filter
        if ($dateFrom = $request->query('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->query('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Branch filter (for super admin)
        if ($user->isSuperAdmin() && $branchFilter = $request->query('branch_filter')) {
            $query->where('branch_id', $branchFilter);
        }

        $patients = $query->orderBy('last_name')->paginate(20);
        
        // Get available branches for filter (if super admin)
        $availableBranches = $user->isSuperAdmin() ? Branch::active()->orderBy('name')->get() : collect();
        
        return view('patients.index', compact('patients', 'availableBranches'));
    }

    public function create()
    {
        $this->authorize('create', Patient::class);
        $branches = $this->availableBranches();
        $doctors = $this->availableDoctors($branches);

        return view('patients.create', compact('branches', 'doctors'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Patient::class);

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'national_id' => 'nullable|string|max:100',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'provider_id' => 'nullable|exists:users,id',
            'visit_type' => 'nullable|in:general,hiv,counseling,lab,pharmacy,other',
            'chief_complaint' => 'nullable|string|max:1000',
        ]);

        $validated['branch_id'] = $this->resolvedBranchIdForWrite($validated['branch_id']);

        if (! empty($validated['provider_id'])) {
            $doctor = User::role('doctor')->whereKey($validated['provider_id'])->firstOrFail();
            if ($doctor->branch_id !== $validated['branch_id']) {
                return back()->withErrors(['provider_id' => 'Selected doctor must belong to the chosen branch.'])->withInput();
            }
        }

        $patient = Patient::create($validated);

        AuditLogService::logAccess($request, 'create', 'patients', 'Created patient: ' . $patient->full_name);

        if (! empty($validated['provider_id']) || ! empty($validated['chief_complaint'])) {
            Visit::create([
                'branch_id' => $validated['branch_id'],
                'patient_id' => $patient->id,
                'provider_id' => $validated['provider_id'] ?? null,
                'visit_date' => now(),
                'visit_type' => $validated['visit_type'] ?? 'general',
                'chief_complaint' => $validated['chief_complaint'] ?? null,
                'notes' => null,
                'status' => 'open',
                'workflow_stage' => Visit::STAGE_CHECKED_IN,
                'checked_in_at' => now(),
            ]);

            return redirect()->route('patients.show', $patient)->with('success', 'Patient registered and visit opened.');
        }

        return redirect()->route('patients.index')->with('success', 'Patient registered successfully.');
    }

    public function show($id)
    {
        $patient = Patient::visibleTo($this->currentUser())
            ->with(['branch', 'visits.provider', 'labTests', 'allergies'])
            ->findOrFail($id);

        $this->authorize('view', $patient);

        $pharmacyForPatient = Pharmacy::where('branch_id', $patient->branch_id)->first();

        return view('patients.show', compact('patient', 'pharmacyForPatient'));
    }

    public function edit($id)
    {
        $patient = Patient::visibleTo($this->currentUser())->with('branch')->findOrFail($id);
        $this->authorize('update', $patient);
        $branches = $this->availableBranches();
        return view('patients.edit', compact('patient', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::visibleTo($this->currentUser())->findOrFail($id);
        $this->authorize('update', $patient);

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'national_id' => 'nullable|string|max:100',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $validated['branch_id'] = $this->resolvedBranchIdForWrite($validated['branch_id']);

        $patient->update($validated);

        AuditLogService::logAccess($request, 'update', 'patients', 'Updated patient: ' . $patient->full_name);

        return redirect()->route('patients.index')->with('success', 'Patient profile updated.');
    }

    public function destroy(Request $request, $id)
    {
        $patient = Patient::visibleTo($this->currentUser())->findOrFail($id);
        $this->authorize('delete', $patient);
        $patient->delete();

        AuditLogService::logAccess($request, 'delete', 'patients', 'Deleted patient: ' . $patient->full_name);

        return redirect()->route('patients.index')->with('success', 'Patient record removed.');
    }

    private function availableBranches()
    {
        $user = $this->currentUser();

        if ($user->hasRole('super_admin')) {
            return Branch::orderBy('name')->get();
        }

        return Branch::whereKey($user->branch_id)->get();
    }

    private function availableDoctors($branches)
    {
        $branchIds = $branches->pluck('id')->filter()->toArray();

        return User::role('doctor')
            ->when($branchIds, fn ($query) => $query->whereIn('branch_id', $branchIds))
            ->orderBy('name')
            ->get();
    }

    private function resolvedBranchIdForWrite(int|string $branchId): int
    {
        $user = $this->currentUser();

        if ($user->hasRole('super_admin')) {
            return (int) $branchId;
        }

        return (int) $user->branch_id;
    }
}

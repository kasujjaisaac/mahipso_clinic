<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Admission;
use App\Models\Bed;
use App\Models\Bill;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdmissionController extends Controller
{
    use ResolvesBranchContext;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessAnyModule(['inpatient_ward', 'pharmacy']), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $branchId = $this->branchFilterId($request);
        $admissions = Admission::with(['patient', 'ward', 'bed', 'currentDoctor'])
            ->visibleTo($request->user(), $branchId)
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->query('search'), function ($query, $search) {
                $query->whereHas('patient', fn ($patient) => $patient
                    ->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('mrn', 'like', "%{$search}%"));
            })
            ->orderByRaw("case when status in ('admitted','ready_for_discharge','pending_clearance') then 0 else 1 end")
            ->orderByDesc('admitted_at')
            ->paginate(20);

        return view('admissions.index', compact('admissions'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

        $visit = null;
        $patient = null;
        if ($request->filled('visit_id')) {
            $visit = Visit::visibleTo($request->user())->with('patient')->findOrFail($request->query('visit_id'));
            $patient = $visit->patient;
            $branchId = $visit->branch_id;
        } elseif ($request->filled('patient_id')) {
            $patient = Patient::visibleTo($request->user())->findOrFail($request->query('patient_id'));
            $branchId = $patient->branch_id;
        } else {
            $branchId = $this->selectedBranchId($request);
        }

        $patients = Patient::visibleTo($request->user(), $branchId)->orderBy('last_name')->get();
        $wards = Ward::with(['beds' => fn ($query) => $query->where('status', Bed::STATUS_AVAILABLE)->orderBy('bed_number')])
            ->visibleTo($request->user(), $branchId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $doctors = User::role('doctor')->where('branch_id', $branchId)->orderBy('name')->get();

        return view('admissions.create', compact('visit', 'patient', 'patients', 'wards', 'doctors'));
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'admitting_doctor_id' => 'nullable|exists:users,id',
            'current_doctor_id' => 'nullable|exists:users,id',
            'ward_id' => 'required|exists:wards,id',
            'bed_id' => 'required|exists:beds,id',
            'admission_type' => 'required|in:emergency,elective,referral,observation,maternity,surgical,other',
            'admitted_at' => 'required|date',
            'expected_discharge_at' => 'nullable|date|after_or_equal:admitted_at',
            'reason_for_admission' => 'required|string|max:5000',
            'provisional_diagnosis' => 'nullable|string|max:5000',
            'current_diagnosis' => 'nullable|string|max:5000',
            'care_plan' => 'nullable|string|max:5000',
            'payment_type' => 'nullable|string|max:100',
            'next_of_kin_name' => 'nullable|string|max:255',
            'next_of_kin_phone' => 'nullable|string|max:100',
            'consent_notes' => 'nullable|string|max:5000',
        ]);

        $patient = Patient::visibleTo($request->user())->findOrFail($validated['patient_id']);
        $ward = Ward::visibleTo($request->user())->whereKey($validated['ward_id'])->where('branch_id', $patient->branch_id)->firstOrFail();
        $bed = Bed::whereKey($validated['bed_id'])->where('ward_id', $ward->id)->where('status', Bed::STATUS_AVAILABLE)->firstOrFail();

        if (! empty($validated['visit_id'])) {
            Visit::visibleTo($request->user())->whereKey($validated['visit_id'])->where('patient_id', $patient->id)->firstOrFail();
        }

        foreach (['admitting_doctor_id', 'current_doctor_id'] as $field) {
            if (! empty($validated[$field])) {
                User::role('doctor')->whereKey($validated[$field])->where('branch_id', $patient->branch_id)->firstOrFail();
            }
        }
        $validated['current_doctor_id'] = $validated['current_doctor_id'] ?? $validated['admitting_doctor_id'] ?? null;

        $admission = DB::transaction(function () use ($validated, $patient, $bed) {
            $admission = Admission::create($validated + [
                'branch_id' => $patient->branch_id,
                'admission_no' => Admission::nextAdmissionNo($patient->branch_id),
                'status' => Admission::STATUS_ADMITTED,
            ]);

            $bed->update(['status' => Bed::STATUS_OCCUPIED]);

            return $admission;
        });

        return redirect()->route('admissions.show', $admission)->with('success', 'Patient admitted.');
    }

    public function show(Admission $admission)
    {
        $this->guardAdmission($admission);
        $admission->load([
            'patient.allergies',
            'visit',
            'ward.beds',
            'bed',
            'admittingDoctor',
            'currentDoctor',
            'notes.author',
            'vitals.recorder',
            'medicationOrders.administrations.administeredBy',
            'medicationOrders.prescriber',
            'transfers.fromWard',
            'transfers.fromBed',
            'transfers.toWard',
            'transfers.toBed',
            'dischargeSummary.preparer',
        ]);

        $wards = Ward::with(['beds' => fn ($query) => $query->whereIn('status', [Bed::STATUS_AVAILABLE, Bed::STATUS_CLEANING])->orderBy('bed_number')])
            ->visibleTo(auth()->user(), $admission->branch_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $bills = Bill::where('patient_id', $admission->patient_id)
            ->when($admission->visit_id, fn ($query) => $query->where('visit_id', $admission->visit_id))
            ->orderByDesc('created_at')
            ->get();

        return view('admissions.show', compact('admission', 'wards', 'bills'));
    }

    public function update(Request $request, Admission $admission)
    {
        $this->guardAdmission($admission);

        $validated = $request->validate([
            'current_doctor_id' => 'nullable|exists:users,id',
            'current_diagnosis' => 'nullable|string|max:5000',
            'care_plan' => 'nullable|string|max:5000',
            'expected_discharge_at' => 'nullable|date',
            'payment_type' => 'nullable|string|max:100',
        ]);

        if (! empty($validated['current_doctor_id'])) {
            User::role('doctor')->whereKey($validated['current_doctor_id'])->where('branch_id', $admission->branch_id)->firstOrFail();
        }

        $admission->update($validated);

        return back()->with('success', 'Admission updated.');
    }

    public function addNote(Request $request, Admission $admission)
    {
        $this->guardAdmission($admission);
        abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

        $validated = $request->validate([
            'note_type' => 'required|in:doctor_round,nursing,care_plan,handover,procedure,other',
            'subjective' => 'nullable|string|max:5000',
            'objective' => 'nullable|string|max:5000',
            'assessment' => 'nullable|string|max:5000',
            'plan' => 'nullable|string|max:5000',
            'note' => 'nullable|string|max:5000',
            'recorded_at' => 'required|date',
        ]);

        $admission->notes()->create($validated + ['author_id' => $request->user()->id]);

        return back()->with('success', 'Inpatient note added.');
    }

    public function addVital(Request $request, Admission $admission)
    {
        $this->guardAdmission($admission);
        abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

        $validated = $request->validate([
            'temperature' => 'nullable|numeric|min:20|max:45',
            'blood_pressure_systolic' => 'nullable|integer|min:40|max:260',
            'blood_pressure_diastolic' => 'nullable|integer|min:20|max:180',
            'pulse' => 'nullable|integer|min:20|max:250',
            'respiratory_rate' => 'nullable|integer|min:5|max:80',
            'oxygen_saturation' => 'nullable|integer|min:0|max:100',
            'weight' => 'nullable|numeric|min:0|max:500',
            'intake_ml' => 'nullable|numeric|min:0',
            'output_ml' => 'nullable|numeric|min:0',
            'pain_score' => 'nullable|integer|min:0|max:10',
            'notes' => 'nullable|string|max:2000',
            'recorded_at' => 'required|date',
        ]);

        $admission->vitals()->create($validated + ['recorded_by' => $request->user()->id]);

        return back()->with('success', 'Vitals recorded.');
    }

    public function addMedication(Request $request, Admission $admission)
    {
        $this->guardAdmission($admission);
        abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

        $validated = $request->validate([
            'medicine_name' => 'required|string|max:255',
            'dose' => 'required|string|max:100',
            'route' => 'nullable|string|max:100',
            'frequency' => 'nullable|string|max:100',
            'start_at' => 'nullable|date',
            'stop_at' => 'nullable|date|after_or_equal:start_at',
            'instructions' => 'nullable|string|max:2000',
        ]);

        $admission->medicationOrders()->create($validated + [
            'prescribed_by' => $request->user()->id,
            'status' => 'active',
        ]);

        return back()->with('success', 'Medication order added.');
    }

    public function administerMedication(Request $request, Admission $admission)
    {
        $this->guardAdmission($admission);
        abort_unless($request->user()->canAccessAnyModule(['inpatient_ward', 'pharmacy']), 403);

        $validated = $request->validate([
            'medication_order_id' => ['required', Rule::exists('medication_orders', 'id')->where('admission_id', $admission->id)],
            'scheduled_at' => 'nullable|date',
            'administered_at' => 'nullable|date',
            'status' => 'required|in:given,missed,refused,held',
            'notes' => 'nullable|string|max:2000',
        ]);

        $order = $admission->medicationOrders()->findOrFail($validated['medication_order_id']);
        $order->administrations()->create($validated + [
            'administered_by' => $request->user()->id,
            'administered_at' => $validated['administered_at'] ?? now(),
        ]);

        return back()->with('success', 'Medication administration recorded.');
    }

    public function transfer(Request $request, Admission $admission)
    {
        $this->guardAdmission($admission);
        abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

        $validated = $request->validate([
            'to_ward_id' => 'required|exists:wards,id',
            'to_bed_id' => 'required|exists:beds,id',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'transferred_at' => 'required|date',
        ]);

        $toWard = Ward::visibleTo($request->user())->whereKey($validated['to_ward_id'])->where('branch_id', $admission->branch_id)->firstOrFail();
        $toBed = Bed::whereKey($validated['to_bed_id'])->where('ward_id', $toWard->id)->where('status', Bed::STATUS_AVAILABLE)->firstOrFail();

        DB::transaction(function () use ($admission, $validated, $toWard, $toBed, $request) {
            $fromBed = $admission->bed;
            $admission->transfers()->create([
                'from_ward_id' => $admission->ward_id,
                'from_bed_id' => $admission->bed_id,
                'to_ward_id' => $toWard->id,
                'to_bed_id' => $toBed->id,
                'requested_by' => $request->user()->id,
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'transferred_at' => $validated['transferred_at'],
            ]);

            $admission->update(['ward_id' => $toWard->id, 'bed_id' => $toBed->id]);
            $fromBed?->update(['status' => Bed::STATUS_CLEANING]);
            $toBed->update(['status' => Bed::STATUS_OCCUPIED]);
        });

        return back()->with('success', 'Patient transferred.');
    }

    public function markReady(Request $request, Admission $admission)
    {
        $this->guardAdmission($admission);
        abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

        $admission->update([
            'status' => Admission::STATUS_READY,
            'discharge_started_at' => $admission->discharge_started_at ?? now(),
        ]);

        return back()->with('success', 'Admission marked ready for discharge.');
    }

    public function updateClearance(Request $request, Admission $admission)
    {
        $this->guardAdmission($admission);

        $field = $request->validate([
            'clearance' => 'required|in:nursing_cleared,pharmacy_cleared,billing_cleared',
        ])['clearance'];

        $allowed = match ($field) {
            'nursing_cleared' => $request->user()->canAccessModule('inpatient_ward'),
            'pharmacy_cleared' => $request->user()->canAccessModule('pharmacy'),
            'billing_cleared' => $request->user()->canAccessAnyModule(['front_office', 'finance']),
        };
        abort_unless($allowed, 403);

        $admission->update([
            $field => true,
            'status' => Admission::STATUS_PENDING_CLEARANCE,
        ]);

        return back()->with('success', 'Clearance updated.');
    }

    public function discharge(Request $request, Admission $admission)
    {
        $this->guardAdmission($admission);
        abort_unless($request->user()->canAccessModule('inpatient_ward'), 403);

        $validated = $request->validate([
            'discharge_type' => 'required|in:improved,referred,against_medical_advice,transferred,deceased,absconded',
            'final_diagnosis' => 'required|string|max:5000',
            'condition_on_discharge' => 'nullable|string|max:5000',
            'procedures_done' => 'nullable|string|max:5000',
            'hospital_course' => 'nullable|string|max:5000',
            'investigations' => 'nullable|string|max:5000',
            'treatment_given' => 'nullable|string|max:5000',
            'discharge_medications' => 'nullable|string|max:5000',
            'follow_up_instructions' => 'nullable|string|max:5000',
            'follow_up_date' => 'nullable|date',
        ]);

        if (! $admission->nursing_cleared || ! $admission->pharmacy_cleared || ! $admission->billing_cleared) {
            return back()->withErrors(['discharge' => 'Nursing, pharmacy, and billing clearance are required before discharge.']);
        }

        DB::transaction(function () use ($admission, $validated, $request) {
            $summary = $validated;
            unset($summary['discharge_type']);

            $admission->dischargeSummary()->updateOrCreate(
                ['admission_id' => $admission->id],
                $summary + ['prepared_by' => $request->user()->id]
            );

            $admission->update([
                'status' => match ($validated['discharge_type']) {
                    'deceased' => Admission::STATUS_DECEASED,
                    'absconded' => Admission::STATUS_ABSCONDED,
                    'transferred' => Admission::STATUS_TRANSFERRED,
                    default => Admission::STATUS_DISCHARGED,
                },
                'discharge_type' => $validated['discharge_type'],
                'discharged_at' => now(),
            ]);

            $admission->bed?->update(['status' => Bed::STATUS_CLEANING]);
            $admission->visit?->moveToStage(Visit::STAGE_COMPLETED);
        });

        return redirect()->route('admissions.show', $admission)->with('success', 'Patient discharged.');
    }

    private function guardAdmission(Admission $admission): void
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->branch_id === $admission->branch_id, 404);
    }
}

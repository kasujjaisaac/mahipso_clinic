<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    use ResolvesBranchContext;

    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Appointment::class, 'appointment');
    }

    /**
     * Show calendar view for appointments.
     */
    public function calendar(Request $request)
    {
        $user = $this->currentUser();
        $branchId = $this->branchFilterId($request);
        $appointments = Appointment::with(['patient', 'doctor'])
            ->visibleTo($user, $branchId)
            ->orderBy('scheduled_at')
            ->get();

        $events = $appointments->map(function ($appt) {
            return [
                'id' => $appt->id,
                'title' => $appt->patient?->full_name . ($appt->doctor ? ' / Dr. ' . $appt->doctor->name : ''),
                'start' => $appt->scheduled_at->toIso8601String(),
                'end' => $appt->scheduled_at->copy()->addMinutes($appt->duration ?? 30)->toIso8601String(),
                'status' => $appt->status,
                'status_label' => ucfirst(str_replace('_', ' ', $appt->status)),
                'service_type' => $appt->service_type ?: 'General',
            ];
        });

        return view('appointments.calendar', [
            'events' => $events,
        ]);
    }

    public function availability(Request $request)
    {
        $user = $this->currentUser();
        $branchId = $this->branchFilterId($request);

        $validated = $request->validate([
            'date' => 'required|date',
            'doctor_id' => 'nullable|exists:users,id',
            'duration' => 'nullable|integer|min:5|max:240',
        ]);

        $date = Carbon::parse($validated['date'])->startOfDay();
        $duration = $validated['duration'] ?? 30;
        $endOfDay = $date->copy()->endOfDay();

        $appointments = Appointment::with(['patient', 'doctor'])
            ->visibleTo($user, $branchId)
            ->when($validated['doctor_id'], fn($query, $doctorId) => $query->where('doctor_id', $doctorId))
            ->whereBetween('scheduled_at', [$date, $endOfDay])
            ->orderBy('scheduled_at')
            ->get();

        $bookedSlots = $appointments->map(function ($appointment) {
            return [
                'id' => $appointment->id,
                'start' => $appointment->scheduled_at->toIso8601String(),
                'end' => $appointment->end_time?->toIso8601String(),
                'status' => $appointment->status,
                'doctor' => optional($appointment->doctor)->name,
                'patient' => optional($appointment->patient)->full_name,
            ];
        });

        return response()->json([
            'date' => $date->toDateString(),
            'doctor_id' => $validated['doctor_id'] ?? null,
            'duration' => $duration,
            'booked_slots' => $bookedSlots,
            'available' => $validated['doctor_id'] ? $bookedSlots->isEmpty() : null,
        ]);
    }

    public function index(Request $request)
    {
        $user = $this->currentUser();
        $branchId = $this->branchFilterId($request);

        $doctors = User::role('doctor')
            ->when($branchId, fn($query) => $query->where('branch_id', $branchId))
            ->orderBy('name')
            ->get();

        $appointments = Appointment::with(['patient', 'doctor', 'branch'])
            ->visibleTo($user, $branchId)
            ->when($request->query('search'), function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('mrn', 'like', "%{$search}%");
                });
            })
            ->when($request->query('doctor_id'), fn($query, $doctorId) => $query->where('doctor_id', $doctorId))
            ->when($request->query('status'), fn($query, $status) => $query->where('status', $status))
            ->when($request->query('start_date'), fn($query, $start) => $query->whereDate('scheduled_at', '>=', $start))
            ->when($request->query('end_date'), fn($query, $end) => $query->whereDate('scheduled_at', '<=', $end))
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('appointments.index', compact('appointments', 'doctors'));
    }

    public function create()
    {
        $branchId = $this->selectedBranchId(request());
        $patients = Patient::where('branch_id', $branchId)->orderBy('last_name')->get();
        $doctors = User::role('doctor')->where('branch_id', $branchId)->orderBy('name')->get();

        return view('appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $this->prepareAppointmentData($request);
        Appointment::create($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment created successfully.');
    }

    public function show(Appointment $appointment)
    {
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $branchId = $appointment->branch_id;
        $patients = Patient::where('branch_id', $branchId)->orderBy('last_name')->get();
        $doctors = User::role('doctor')->where('branch_id', $branchId)->orderBy('name')->get();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $this->prepareAppointmentData($request, $appointment);
        $appointment->update($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }

    protected function prepareAppointmentData(Request $request, ?Appointment $appointment = null): array
    {
        $validated = $request->validate($this->appointmentRules($appointment !== null));

        $patient = Patient::visibleTo($this->currentUser())->findOrFail($validated['patient_id']);

        if (! empty($validated['doctor_id'])) {
            User::role('doctor')
                ->whereKey($validated['doctor_id'])
                ->where('branch_id', $patient->branch_id)
                ->firstOrFail();
        }

        $validated['branch_id'] = $patient->branch_id;
        $validated['status'] = $validated['status'] ?? 'scheduled';

        $this->ensureNoOverlap($validated, $appointment);

        return $validated;
    }

    protected function appointmentRules(bool $isUpdate = false): array
    {
        $rules = [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:users,id',
            'scheduled_at' => 'required|date|after:now',
            'duration' => 'required|integer|min:5|max:240',
            'service_type' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];

        if ($isUpdate) {
            $rules['status'] = 'required|in:' . implode(',', Appointment::STATUSES);
        }

        return $rules;
    }

    protected function ensureNoOverlap(array $validated, ?Appointment $appointment = null): void
    {
        $start = Carbon::parse($validated['scheduled_at']);
        $end = $start->copy()->addMinutes($validated['duration']);
        $ignoreId = $appointment?->id;

        if (! empty($validated['doctor_id'])) {
            $doctorConflict = Appointment::where('doctor_id', $validated['doctor_id'])
                ->where('branch_id', $validated['branch_id'])
                ->when($ignoreId, fn($query) => $query->where('id', '<>', $ignoreId))
                ->where('scheduled_at', '<', $end)
                ->whereRaw('DATE_ADD(scheduled_at, INTERVAL duration MINUTE) > ?', [$start])
                ->exists();

            if ($doctorConflict) {
                throw ValidationException::withMessages([
                    'doctor_id' => 'The selected doctor already has an appointment during this time.',
                ]);
            }
        }

        $patientConflict = Appointment::where('patient_id', $validated['patient_id'])
            ->where('branch_id', $validated['branch_id'])
            ->when($ignoreId, fn($query) => $query->where('id', '<>', $ignoreId))
            ->where('scheduled_at', '<', $end)
            ->whereRaw('DATE_ADD(scheduled_at, INTERVAL duration MINUTE) > ?', [$start])
            ->exists();

        if ($patientConflict) {
            throw ValidationException::withMessages([
                'scheduled_at' => 'This patient already has another appointment during the selected time window.',
            ]);
        }
    }
}

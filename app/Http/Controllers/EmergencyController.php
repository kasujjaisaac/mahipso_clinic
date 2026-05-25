<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emergency;
use App\Models\Patient;

class EmergencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('programs'), 403);

            return $next($request);
        });
    }

    public function index()
    {
        $emergencies = Emergency::when(! auth()->user()->isSuperAdmin(), function ($query) {
                $query->whereHas('patient', fn ($patientQuery) => $patientQuery->where('branch_id', auth()->user()->branch_id));
            })
            ->latest()
            ->paginate(20);

        return view('emergencies.index', compact('emergencies'));
    }

    public function create()
    {
        return view('emergencies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'reported_at' => 'required|date',
        ]);

        Patient::visibleTo($request->user())->whereKey($validated['patient_id'])->firstOrFail();

        $emergency = Emergency::create($validated);
        return redirect()->route('emergencies.show', $emergency)->with('success', 'Emergency recorded successfully.');
    }

    public function show($id)
    {
        $emergency = Emergency::with('patient')
            ->when(! auth()->user()->isSuperAdmin(), function ($query) {
                $query->whereHas('patient', fn ($patientQuery) => $patientQuery->where('branch_id', auth()->user()->branch_id));
            })
            ->findOrFail($id);

        return view('emergencies.show', compact('emergency'));
    }

    public function edit($id)
    {
        $emergency = Emergency::when(! auth()->user()->isSuperAdmin(), function ($query) {
                $query->whereHas('patient', fn ($patientQuery) => $patientQuery->where('branch_id', auth()->user()->branch_id));
            })
            ->findOrFail($id);

        return view('emergencies.edit', compact('emergency'));
    }

    public function update(Request $request, $id)
    {
        $emergency = Emergency::when(! $request->user()->isSuperAdmin(), function ($query) use ($request) {
                $query->whereHas('patient', fn ($patientQuery) => $patientQuery->where('branch_id', $request->user()->branch_id));
            })
            ->findOrFail($id);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'reported_at' => 'required|date',
        ]);

        Patient::visibleTo($request->user())->whereKey($validated['patient_id'])->firstOrFail();

        $emergency->update($validated);
        return redirect()->route('emergencies.show', $emergency)->with('success', 'Emergency updated successfully.');
    }

    public function destroy($id)
    {
        $emergency = Emergency::when(! auth()->user()->isSuperAdmin(), function ($query) {
                $query->whereHas('patient', fn ($patientQuery) => $patientQuery->where('branch_id', auth()->user()->branch_id));
            })
            ->findOrFail($id);

        $emergency->delete();
        return redirect()->route('emergencies.index')->with('success', 'Emergency deleted.');
    }
}

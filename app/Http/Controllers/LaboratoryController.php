<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\Patient;
use App\Models\ServiceItem;
use App\Models\Visit;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessAnyModule(['clinic', 'laboratory']), 403);

            return $next($request);
        });
    }

    public function index() {
        $labTests = LabTest::visibleTo(auth()->user())->with('patient')->orderByDesc('ordered_at')->paginate(15);
        return view('laboratory.index', compact('labTests'));
    }
    public function create(Request $request) {
        $visit = $request->query('visit_id')
            ? Visit::visibleTo($request->user())->with('patient')->findOrFail($request->query('visit_id'))
            : null;
        $patients = Patient::visibleTo($request->user())->orderBy('last_name')->get();
        $services = ServiceItem::visibleTo($request->user())->where('category', 'laboratory')->where('is_active', true)->orderBy('name')->get();

        return view('laboratory.create', compact('visit', 'patients', 'services'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'test_type' => 'required|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'ordered_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        $patient = Patient::visibleTo($request->user())->findOrFail($validated['patient_id']);

        if (! empty($validated['visit_id'])) {
            Visit::visibleTo($request->user())
                ->whereKey($validated['visit_id'])
                ->where('patient_id', $patient->id)
                ->firstOrFail();
        }

        $validated['ordered_by'] = $request->user()->id;
        $validated['price'] = $validated['price'] ?? 0;

        $labTest = LabTest::create($validated);

        if (! empty($validated['visit_id'])) {
            $labTest->visit->moveToStage(Visit::STAGE_LABORATORY);
        }

        return redirect()->route('laboratory.show', $labTest)->with('success', 'Lab test created successfully.');
    }

    public function show($id) {
        $labTest = LabTest::visibleTo(auth()->user())->with('patient')->findOrFail($id);
        return view('laboratory.show', compact('labTest'));
    }

    public function edit($id) {
        $labTest = LabTest::visibleTo(auth()->user())->findOrFail($id);
        return view('laboratory.edit', compact('labTest'));
    }

    public function update(Request $request, $id) {
        $labTest = LabTest::visibleTo($request->user())->findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'test_type' => 'required|string|max:255',
            'status' => 'required|in:ordered,in_progress,completed,cancelled',
            'price' => 'nullable|numeric|min:0',
            'ordered_at' => 'required|date',
            'completed_at' => 'nullable|date',
            'results' => 'nullable|string',
            'result_flag' => 'nullable|in:normal,abnormal,critical',
            'notes' => 'nullable|string',
        ]);
        $patient = Patient::visibleTo($request->user())->findOrFail($validated['patient_id']);

        if (! empty($validated['visit_id'])) {
            Visit::visibleTo($request->user())
                ->whereKey($validated['visit_id'])
                ->where('patient_id', $patient->id)
                ->firstOrFail();
        }

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = $validated['completed_at'] ?? now()->toDateString();
            $validated['resulted_by'] = $request->user()->id;
            $labTest->visit?->moveToStage(Visit::STAGE_CONSULTATION);
        }

        $labTest->update($validated);
        return redirect()->route('laboratory.show', $labTest)->with('success', 'Lab test updated successfully.');
    }

    public function destroy($id) {
        $labTest = LabTest::visibleTo(auth()->user())->findOrFail($id);
        $labTest->delete();
        return redirect()->route('laboratory.index')->with('success', 'Lab test deleted successfully.');
    }
}

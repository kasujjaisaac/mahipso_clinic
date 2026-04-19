<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    public function index() {
        $labTests = \App\Models\LabTest::with('patient')->orderByDesc('ordered_at')->paginate(15);
        return view('laboratory.index', compact('labTests'));
    }
    public function create() {
        return view('laboratory.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'test_type' => 'required|string|max:255',
            'ordered_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        $labTest = \App\Models\LabTest::create($validated);
        return redirect()->route('laboratory.show', $labTest)->with('success', 'Lab test created successfully.');
    }

    public function show($id) {
        $labTest = \App\Models\LabTest::with('patient')->findOrFail($id);
        return view('laboratory.show', compact('labTest'));
    }

    public function edit($id) {
        $labTest = \App\Models\LabTest::findOrFail($id);
        return view('laboratory.edit', compact('labTest'));
    }

    public function update(Request $request, $id) {
        $labTest = \App\Models\LabTest::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'test_type' => 'required|string|max:255',
            'status' => 'required|string',
            'ordered_at' => 'required|date',
            'completed_at' => 'nullable|date',
            'results' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $labTest->update($validated);
        return redirect()->route('laboratory.show', $labTest)->with('success', 'Lab test updated successfully.');
    }

    public function destroy($id) {
        $labTest = \App\Models\LabTest::findOrFail($id);
        $labTest->delete();
        return redirect()->route('laboratory.index')->with('success', 'Lab test deleted successfully.');
    }
}

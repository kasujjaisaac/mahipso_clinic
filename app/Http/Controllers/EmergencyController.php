<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emergency;

class EmergencyController extends Controller
{
    public function index()
    {
        $emergencies = Emergency::latest()->paginate(20);
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
        $emergency = Emergency::create($validated);
        return redirect()->route('emergencies.show', $emergency)->with('success', 'Emergency recorded successfully.');
    }

    public function show($id)
    {
        $emergency = Emergency::with('patient')->findOrFail($id);
        return view('emergencies.show', compact('emergency'));
    }

    public function edit($id)
    {
        $emergency = Emergency::findOrFail($id);
        return view('emergencies.edit', compact('emergency'));
    }

    public function update(Request $request, $id)
    {
        $emergency = Emergency::findOrFail($id);
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'reported_at' => 'required|date',
        ]);
        $emergency->update($validated);
        return redirect()->route('emergencies.show', $emergency)->with('success', 'Emergency updated successfully.');
    }

    public function destroy($id)
    {
        $emergency = Emergency::findOrFail($id);
        $emergency->delete();
        return redirect()->route('emergencies.index')->with('success', 'Emergency deleted.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientAllergy;
use Illuminate\Http\Request;

class PatientAllergyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessAnyModule(['clinic', 'nursing']), 403);

            return $next($request);
        });
    }

    public function store(Request $request, Patient $patient)
    {
        $patient = Patient::visibleTo($request->user())->findOrFail($patient->id);
        $data = $request->validate([
            'substance' => 'required|string|max:255',
            'reaction' => 'nullable|string|max:255',
            'severity' => 'required|in:mild,moderate,severe,unknown',
        ]);
        $data['recorded_by'] = $request->user()->id;
        $patient->allergies()->create($data);
        return back()->with('success', 'Allergy recorded.');
    }

    public function destroy(PatientAllergy $allergy)
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->branch_id === $allergy->patient->branch_id, 404);
        $allergy->delete();
        return back()->with('success', 'Allergy removed.');
    }
}

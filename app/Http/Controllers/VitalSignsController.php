<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\VitalSigns;
use Illuminate\Http\Request;

class VitalSignsController extends Controller
{
    public function create(Visit $visit)
    {
        return view('vitals.create', compact('visit'));
    }

    public function store(Request $request, Visit $visit)
    {
        $data = $request->validate([
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'blood_pressure_systolic' => 'nullable|numeric',
            'blood_pressure_diastolic' => 'nullable|numeric',
            'pulse' => 'nullable|numeric',
            'respiratory_rate' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
        $data['visit_id'] = $visit->id;
        VitalSigns::updateOrCreate(['visit_id' => $visit->id], $data);
        return redirect()->route('visits.show', $visit)->with('success', 'Vital signs recorded.');
    }
}

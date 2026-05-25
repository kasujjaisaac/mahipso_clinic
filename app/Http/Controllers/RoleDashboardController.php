<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\PrescriptionOrder;
use App\Models\Visit;
use Illuminate\Http\Request;

class RoleDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request)
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('doctor')) {
            $visits = Visit::visibleTo($user)->with('patient')->where('workflow_stage', Visit::STAGE_CONSULTATION)->where('status', 'open')->latest('visit_date')->limit(10)->get();
            $labResults = LabTest::visibleTo($user)->with('patient')->where('status', 'completed')->latest('completed_at')->limit(10)->get();
            return view('dashboards.doctor', compact('visits', 'labResults'));
        }

        if ($user->hasRole('nurse')) {
            $visits = Visit::visibleTo($user)->with('patient')->whereIn('workflow_stage', [Visit::STAGE_CHECKED_IN, Visit::STAGE_TRIAGE])->where('status', 'open')->latest('visit_date')->limit(15)->get();
            return view('dashboards.nurse', compact('visits'));
        }

        if ($user->hasRole('labtech')) {
            $labTests = LabTest::visibleTo($user)->with('patient')->whereIn('status', ['ordered', 'in_progress'])->latest('ordered_at')->limit(15)->get();
            return view('dashboards.labtech', compact('labTests'));
        }

        if ($user->hasRole('pharmacist')) {
            $orders = PrescriptionOrder::visibleTo($user)->with(['patient', 'items.product'])->whereIn('status', ['pending', 'partially_dispensed'])->latest('ordered_at')->limit(15)->get();
            return view('dashboards.pharmacist', compact('orders'));
        }

        if ($user->hasRole('receptionist')) {
            $appointments = Appointment::visibleTo($user)->with('patient')->whereDate('scheduled_at', today())->orderBy('scheduled_at')->limit(15)->get();
            $bills = Bill::visibleTo($user)->with('patient')->whereIn('status', ['unpaid', 'partial'])->latest('billed_at')->limit(10)->get();
            return view('dashboards.receptionist', compact('appointments', 'bills'));
        }

        if ($user->hasRole('patient')) {
            $patient = Patient::where('email', $user->email)->with(['visits', 'labTests', 'allergies'])->first();
            $bills = $patient ? Bill::where('patient_id', $patient->id)->latest('billed_at')->limit(10)->get() : collect();
            return view('dashboards.patient', compact('patient', 'bills'));
        }

        return redirect()->route('branches.cards');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\LabTest;
use App\Models\MonthlyTimesheet;
use App\Models\PrescriptionOrder;
use App\Models\StaffAppraisal;
use App\Models\StaffAttendance;
use App\Models\Visit;
use Illuminate\Http\Request;

class StaffPortalController extends Controller
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

        $role = $this->primaryRole($user);
        $cards = $this->summaryCards($user);
        $workQueue = $this->workQueue($user);
        $doctorInpatients = $user->hasRole('doctor') ? $this->doctorInpatients($user) : collect();
        $nurseInpatients = $user->hasRole('nurse') ? $this->nurseInpatients($user) : collect();
        $recentLabResults = $user->hasRole('doctor')
            ? LabTest::visibleTo($user)->with('patient')->where('status', 'completed')->latest('completed_at')->limit(8)->get()
            : collect();
        $lowStock = $user->hasRole(['pharmacist', 'branch_admin'])
            ? Inventory::visibleTo($user)->whereColumn('quantity', '<=', 'reorder_level')->orderBy('quantity')->limit(8)->get()
            : collect();

        return view('staff.dashboard', compact(
            'role',
            'cards',
            'workQueue',
            'doctorInpatients',
            'nurseInpatients',
            'recentLabResults',
            'lowStock'
        ));
    }

    private function primaryRole($user): string
    {
        foreach (['doctor', 'nurse', 'labtech', 'pharmacist', 'receptionist', 'finance_officer', 'hr_manager', 'branch_admin'] as $role) {
            if ($user->hasRole($role)) {
                return ucfirst(str_replace('_', ' ', $role));
            }
        }

        return 'Staff';
    }

    private function summaryCards($user): array
    {
        if ($user->hasRole('hr_manager') && ! $user->hasRole('branch_admin')) {
            return [
                ['label' => 'Active staff', 'value' => Employee::visibleTo($user)->where('status', 'active')->count(), 'accent' => '#2f7d57'],
                ['label' => 'Present today', 'value' => StaffAttendance::visibleTo($user)->whereDate('work_date', today())->where('status', 'present')->count(), 'accent' => '#2f6fed'],
                ['label' => 'On leave today', 'value' => StaffAttendance::visibleTo($user)->whereDate('work_date', today())->where('status', 'leave')->count(), 'accent' => '#c87b16'],
                ['label' => 'Pending appraisals', 'value' => StaffAppraisal::visibleTo($user)->whereIn('status', ['draft', 'pending', 'in_progress', 'submitted'])->count(), 'accent' => '#7c3aed'],
            ];
        }

        $openVisits = Visit::visibleTo($user)->where('status', 'open');
        $admissions = Admission::visibleTo($user)->whereIn('status', [
            Admission::STATUS_ADMITTED,
            Admission::STATUS_READY,
            Admission::STATUS_PENDING_CLEARANCE,
        ]);

        $cards = [
            ['label' => 'Open visits', 'value' => (clone $openVisits)->count(), 'accent' => '#b8342b'],
            ['label' => 'Inpatients', 'value' => (clone $admissions)->count(), 'accent' => '#2f7d57'],
        ];

        if ($user->hasRole(['doctor', 'nurse', 'receptionist', 'branch_admin'])) {
            $cards[] = ['label' => 'Triage waiting', 'value' => (clone $openVisits)->whereIn('workflow_stage', [Visit::STAGE_CHECKED_IN, Visit::STAGE_TRIAGE])->count(), 'accent' => '#c87b16'];
            $cards[] = ['label' => 'Consultation waiting', 'value' => (clone $openVisits)->where('workflow_stage', Visit::STAGE_CONSULTATION)->count(), 'accent' => '#2f6fed'];
        }

        if ($user->hasRole(['labtech', 'doctor', 'branch_admin'])) {
            $cards[] = ['label' => 'Pending lab tests', 'value' => LabTest::visibleTo($user)->whereIn('status', ['ordered', 'in_progress'])->count(), 'accent' => '#7c3aed'];
        }

        if ($user->hasRole(['pharmacist', 'branch_admin'])) {
            $cards[] = ['label' => 'Prescriptions due', 'value' => PrescriptionOrder::visibleTo($user)->whereIn('status', ['pending', 'partially_dispensed'])->count(), 'accent' => '#2f7d57'];
        }

        if ($user->hasRole(['receptionist', 'finance_officer', 'branch_admin'])) {
            $cards[] = ['label' => 'Outstanding bills', 'value' => Bill::visibleTo($user)->whereIn('status', ['unpaid', 'partial'])->count(), 'accent' => '#b8342b'];
        }

        return $cards;
    }

    private function workQueue($user)
    {
        if ($user->hasRole('hr_manager') && ! $user->hasRole('branch_admin')) {
            return MonthlyTimesheet::visibleTo($user)->with('user')
                ->whereIn('status', ['submitted', 'supervisor_approved'])
                ->latest('updated_at')
                ->limit(12)
                ->get()
                ->map(fn ($timesheet) => [
                    'type' => 'Timesheet',
                    'patient' => $timesheet->user->name ?? 'Staff member',
                    'status' => ucfirst(str_replace('_', ' ', $timesheet->status)),
                    'time' => optional($timesheet->updated_at)->format('M d, H:i'),
                    'action' => 'Review',
                    'url' => route('timesheets.show', $timesheet),
                ]);
        }

        if ($user->hasRole('doctor')) {
            return Visit::visibleTo($user)->with('patient')
                ->where('workflow_stage', Visit::STAGE_CONSULTATION)
                ->where('status', 'open')
                ->latest('visit_date')
                ->limit(12)
                ->get()
                ->map(fn ($visit) => [
                    'type' => 'Consultation',
                    'patient' => $visit->patient->full_name ?? 'N/A',
                    'status' => $visit->workflow_stage_label,
                    'time' => optional($visit->visit_date)->format('M d, H:i'),
                    'action' => 'Open visit',
                    'url' => route('visits.show', $visit),
                ]);
        }

        if ($user->hasRole('nurse')) {
            return Visit::visibleTo($user)->with('patient')
                ->whereIn('workflow_stage', [Visit::STAGE_CHECKED_IN, Visit::STAGE_TRIAGE])
                ->where('status', 'open')
                ->latest('visit_date')
                ->limit(12)
                ->get()
                ->map(fn ($visit) => [
                    'type' => 'Triage',
                    'patient' => $visit->patient->full_name ?? 'N/A',
                    'status' => $visit->workflow_stage_label,
                    'time' => optional($visit->visit_date)->format('M d, H:i'),
                    'action' => 'Record triage',
                    'url' => route('visits.show', $visit),
                ]);
        }

        if ($user->hasRole('labtech')) {
            return LabTest::visibleTo($user)->with('patient')
                ->whereIn('status', ['ordered', 'in_progress'])
                ->latest('ordered_at')
                ->limit(12)
                ->get()
                ->map(fn ($test) => [
                    'type' => 'Lab test',
                    'patient' => $test->patient->full_name ?? 'N/A',
                    'status' => ucfirst(str_replace('_', ' ', $test->status)),
                    'time' => optional($test->ordered_at)->format('M d'),
                    'action' => 'Enter result',
                    'url' => route('laboratory.edit', $test),
                ]);
        }

        if ($user->hasRole('pharmacist')) {
            return PrescriptionOrder::visibleTo($user)->with('patient')
                ->whereIn('status', ['pending', 'partially_dispensed'])
                ->latest('ordered_at')
                ->limit(12)
                ->get()
                ->map(fn ($order) => [
                    'type' => 'Prescription',
                    'patient' => $order->patient->full_name ?? 'N/A',
                    'status' => ucfirst(str_replace('_', ' ', $order->status)),
                    'time' => optional($order->ordered_at)->format('M d, H:i'),
                    'action' => 'Dispense',
                    'url' => route('prescriptions.show', $order),
                ]);
        }

        if ($user->hasRole('receptionist')) {
            return Appointment::visibleTo($user)->with('patient')
                ->whereDate('scheduled_at', today())
                ->orderBy('scheduled_at')
                ->limit(12)
                ->get()
                ->map(fn ($appointment) => [
                    'type' => 'Appointment',
                    'patient' => $appointment->patient->full_name ?? 'N/A',
                    'status' => $appointment->status_label,
                    'time' => optional($appointment->scheduled_at)->format('H:i'),
                    'action' => 'Open',
                    'url' => route('appointments.show', $appointment),
                ]);
        }

        if ($user->hasRole('finance_officer')) {
            return Bill::visibleTo($user)->with('patient')
                ->whereIn('status', ['unpaid', 'partial'])
                ->latest('billed_at')
                ->limit(12)
                ->get()
                ->map(fn ($bill) => [
                    'type' => 'Bill',
                    'patient' => $bill->patient->full_name ?? 'N/A',
                    'status' => 'Balance USh '.number_format($bill->balance, 0),
                    'time' => optional($bill->billed_at)->format('M d, H:i'),
                    'action' => 'Receive payment',
                    'url' => route('billing.show', $bill),
                ]);
        }

        if ($user->hasRole('branch_admin')) {
            return Admission::visibleTo($user)->with(['patient', 'ward', 'bed'])
                ->whereIn('status', [Admission::STATUS_ADMITTED, Admission::STATUS_READY, Admission::STATUS_PENDING_CLEARANCE])
                ->latest('admitted_at')
                ->limit(12)
                ->get()
                ->map(fn ($admission) => [
                    'type' => 'Admission',
                    'patient' => $admission->patient->full_name ?? 'N/A',
                    'status' => $admission->status_label,
                    'time' => optional($admission->admitted_at)->format('M d, H:i'),
                    'action' => 'Open chart',
                    'url' => route('admissions.show', $admission),
                ]);
        }

        return Appointment::visibleTo($user)->with('patient')
            ->whereDate('scheduled_at', today())
            ->orderBy('scheduled_at')
            ->limit(12)
            ->get()
            ->map(fn ($appointment) => [
                'type' => 'Appointment',
                'patient' => $appointment->patient->full_name ?? 'N/A',
                'status' => $appointment->status_label,
                'time' => optional($appointment->scheduled_at)->format('H:i'),
                'action' => 'Open',
                'url' => route('appointments.show', $appointment),
            ]);
    }

    private function doctorInpatients($user)
    {
        return Admission::visibleTo($user)->with(['patient', 'ward', 'bed'])
            ->whereIn('status', [Admission::STATUS_ADMITTED, Admission::STATUS_READY, Admission::STATUS_PENDING_CLEARANCE])
            ->where(function ($query) use ($user) {
                $query->where('current_doctor_id', $user->id)
                    ->orWhere('admitting_doctor_id', $user->id)
                    ->orWhereNull('current_doctor_id');
            })
            ->latest('admitted_at')
            ->limit(10)
            ->get();
    }

    private function nurseInpatients($user)
    {
        return Admission::visibleTo($user)->with(['patient', 'ward', 'bed', 'vitals'])
            ->whereIn('status', [Admission::STATUS_ADMITTED, Admission::STATUS_READY, Admission::STATUS_PENDING_CLEARANCE])
            ->latest('admitted_at')
            ->limit(10)
            ->get();
    }
}

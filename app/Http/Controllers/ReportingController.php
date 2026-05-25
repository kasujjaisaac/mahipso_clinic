<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\LabTest;
use App\Models\Patient;
use App\Models\PaymentTransaction;
use App\Models\PrescriptionOrder;
use App\Models\PurchaseOrder;
use App\Models\StaffAttendance;
use App\Models\Visit;
use Illuminate\Http\Request;

class ReportingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('programs'), 403);
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $branchScope = fn ($query, $column = 'branch_id') => $query->when(! $request->user()->isSuperAdmin(), fn ($q) => $q->where($column, $request->user()->branch_id));

        $financial = [
            'total_billed' => (float) $branchScope(Bill::query()->whereHas('patient', fn ($q) => $branchScope($q)))->sum('amount'),
            'total_collected' => (float) $branchScope(PaymentTransaction::query())->sum('amount'),
            'outstanding' => (float) $branchScope(Bill::query()->whereHas('patient', fn ($q) => $branchScope($q)))->sum(\DB::raw('amount - paid')),
            'total_bills' => $branchScope(Bill::query()->whereHas('patient', fn ($q) => $branchScope($q)))->count(),
        ];

        $clinical = [
            'total_patients' => $branchScope(Patient::query())->count(),
            'open_visits' => $branchScope(Visit::query())->where('status', 'open')->count(),
            'appointments_today' => $branchScope(Appointment::query())->whereDate('scheduled_at', today())->count(),
            'pending_lab_tests' => LabTest::visibleTo($request->user())->whereIn('status', ['ordered', 'in_progress'])->count(),
            'pending_prescriptions' => PrescriptionOrder::visibleTo($request->user())->whereIn('status', ['pending', 'partially_dispensed'])->count(),
        ];

        $operational = [
            'total_employees' => $branchScope(Employee::query())->count(),
            'total_branches' => $request->user()->isSuperAdmin() ? Branch::count() : 1,
            'inventory_items' => Inventory::visibleTo($request->user())->count(),
            'purchase_orders_open' => PurchaseOrder::visibleTo($request->user())->whereIn('status', ['draft', 'submitted', 'approved'])->count(),
            'attendance_today' => StaffAttendance::visibleTo($request->user())->whereDate('work_date', today())->count(),
        ];

        return view('reporting.index', compact('financial', 'clinical', 'operational'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\PayrollRun;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessAnyModule(['human_resources', 'finance']), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $payrollRuns = PayrollRun::visibleTo($request->user())
            ->with('branch')
            ->latest('period_month')
            ->paginate(20);

        return view('payroll.index', compact('payrollRuns'));
    }

    public function create(Request $request)
    {
        $branches = $request->user()->isSuperAdmin()
            ? Branch::active()->orderBy('name')->get()
            : Branch::whereKey($request->user()->branch_id)->get();

        return view('payroll.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'period_month' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $branchId = $request->user()->isSuperAdmin() ? ($data['branch_id'] ?? null) : $request->user()->branch_id;
        $periodMonth = Carbon::parse($data['period_month'])->startOfMonth();

        $payroll = DB::transaction(function () use ($request, $branchId, $periodMonth, $data) {
            $payroll = PayrollRun::create([
                'branch_id' => $branchId,
                'period_month' => $periodMonth,
                'prepared_by' => $request->user()->id,
                'notes' => $data['notes'] ?? null,
            ]);

            $employees = Employee::query()
                ->where('status', 'active')
                ->when($branchId, fn ($query) => $query->where('branch_id', $branchId))
                ->with(['contracts' => fn ($query) => $query->where('status', 'active')->latest('start_date')])
                ->orderBy('last_name')
                ->get();

            foreach ($employees as $employee) {
                $basicPay = (float) optional($employee->contracts->first())->salary_amount;
                $payroll->items()->create([
                    'employee_id' => $employee->id,
                    'basic_pay' => $basicPay,
                    'allowances' => 0,
                    'deductions' => 0,
                    'net_pay' => $basicPay,
                ]);
            }

            $this->recalculate($payroll);

            return $payroll;
        });

        return redirect()->route('payroll.show', $payroll)->with('success', 'Payroll run prepared.');
    }

    public function show(Request $request, PayrollRun $payroll)
    {
        $this->authorizeVisible($request, $payroll);
        $payroll->load(['branch', 'items.employee.department', 'preparedBy', 'approvedBy']);

        return view('payroll.show', compact('payroll'));
    }

    public function updateItem(Request $request, PayrollRun $payroll)
    {
        $this->authorizeVisible($request, $payroll);
        abort_unless($payroll->status === 'draft', 422);

        $data = $request->validate([
            'item_id' => 'required|exists:payroll_items,id',
            'basic_pay' => 'required|numeric|min:0',
            'allowances' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $item = $payroll->items()->whereKey($data['item_id'])->firstOrFail();
        $item->update([
            'basic_pay' => $data['basic_pay'],
            'allowances' => $data['allowances'],
            'deductions' => $data['deductions'],
            'net_pay' => ((float) $data['basic_pay']) + ((float) $data['allowances']) - ((float) $data['deductions']),
            'notes' => $data['notes'] ?? null,
        ]);

        $this->recalculate($payroll);

        return back()->with('success', 'Payroll item updated.');
    }

    public function approve(Request $request, PayrollRun $payroll)
    {
        $this->authorizeVisible($request, $payroll);
        $payroll->update(['status' => 'approved', 'approved_by' => $request->user()->id]);

        return back()->with('success', 'Payroll approved.');
    }

    public function markPaid(Request $request, PayrollRun $payroll)
    {
        $this->authorizeVisible($request, $payroll);
        $payroll->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('success', 'Payroll marked as paid.');
    }

    private function recalculate(PayrollRun $payroll): void
    {
        $payroll->load('items');
        $payroll->update([
            'gross_total' => $payroll->items->sum(fn ($item) => (float) $item->basic_pay + (float) $item->allowances),
            'deductions_total' => $payroll->items->sum(fn ($item) => (float) $item->deductions),
            'net_total' => $payroll->items->sum(fn ($item) => (float) $item->net_pay),
        ]);
    }

    private function authorizeVisible(Request $request, PayrollRun $payroll): void
    {
        abort_unless(PayrollRun::visibleTo($request->user())->whereKey($payroll->id)->exists(), 404);
    }
}

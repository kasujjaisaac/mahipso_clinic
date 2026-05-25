<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeContract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('human_resources'), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $contracts = EmployeeContract::visibleTo($request->user())
            ->with(['employee.branch', 'employee.department'])
            ->latest('start_date')
            ->paginate(20);

        return view('contracts.index', compact('contracts'));
    }

    public function create(Request $request)
    {
        $employees = Employee::visibleTo($request->user())->orderBy('last_name')->get();
        $contract = new EmployeeContract([
            'contract_no' => 'CON-' . now()->format('Ymd-His'),
            'start_date' => now()->toDateString(),
            'status' => 'draft',
        ]);

        return view('contracts.create', compact('contract', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $employee = Employee::visibleTo($request->user())->findOrFail($data['employee_id']);
        $data['branch_id'] = $employee->branch_id;

        EmployeeContract::create($data);

        return redirect()->route('contracts.index')->with('success', 'Contract saved.');
    }

    public function show(Request $request, EmployeeContract $contract)
    {
        $this->authorizeVisible($request, $contract);
        $contract->load(['employee.branch', 'employee.department']);

        return view('contracts.show', compact('contract'));
    }

    public function edit(Request $request, EmployeeContract $contract)
    {
        $this->authorizeVisible($request, $contract);
        $employees = Employee::visibleTo($request->user())->orderBy('last_name')->get();

        return view('contracts.edit', compact('contract', 'employees'));
    }

    public function update(Request $request, EmployeeContract $contract)
    {
        $this->authorizeVisible($request, $contract);
        $data = $this->validatedData($request, $contract);
        $employee = Employee::visibleTo($request->user())->findOrFail($data['employee_id']);
        $data['branch_id'] = $employee->branch_id;
        $contract->update($data);

        return redirect()->route('contracts.show', $contract)->with('success', 'Contract updated.');
    }

    public function destroy(Request $request, EmployeeContract $contract)
    {
        $this->authorizeVisible($request, $contract);
        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Contract deleted.');
    }

    private function validatedData(Request $request, ?EmployeeContract $contract = null): array
    {
        $contractId = $contract?->id ?? 'NULL';

        return $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'contract_no' => 'required|string|max:100|unique:employee_contracts,contract_no,' . $contractId,
            'contract_type' => 'required|in:permanent,fixed_term,part_time,volunteer,consultant',
            'job_title' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'salary_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,expired,terminated',
            'terms' => 'nullable|string',
            'signed_at' => 'nullable|date',
        ]);
    }

    private function authorizeVisible(Request $request, EmployeeContract $contract): void
    {
        abort_unless(EmployeeContract::visibleTo($request->user())->whereKey($contract->id)->exists(), 404);
    }
}

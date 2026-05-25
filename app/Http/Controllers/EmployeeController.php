<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Branch;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('human_resources'), 403);

            return $next($request);
        });
    }

    public function index()
    {
        $employees = Employee::visibleTo(auth()->user())->with(['department','branch'])->orderBy('last_name')->paginate(20);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        $branches = $this->availableBranches();
        $roles = $this->employeeRoles();
        return view('employees.create', compact('departments','branches', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_no' => 'required|unique:employees,employee_no',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable',
            'department_id' => 'nullable|exists:departments,id',
            'branch_id' => 'nullable|exists:branches,id',
            'job_title' => 'nullable',
            'role_name' => ['nullable', 'exists:roles,name', Rule::notIn(['super_admin', 'patient'])],
            'status' => 'required|in:active,on_leave,terminated',
            'hire_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
        ]);
        $data['branch_id'] = $this->resolvedBranchId($data['branch_id'] ?? null);

        Employee::create($data);
        return redirect()->route('employees.index')->with('success','Employee added.');
    }

    public function show(Employee $employee)
    {
        $this->authorizeBranchAccess($employee);
        $employee->load([
            'department',
            'branch',
            'contracts' => fn ($query) => $query->latest('start_date')->limit(5),
            'appraisals' => fn ($query) => $query->latest('period_end')->limit(5),
            'payrollItems.payrollRun',
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $this->authorizeBranchAccess($employee);

        $departments = Department::all();
        $branches = $this->availableBranches();
        $roles = $this->employeeRoles();
        return view('employees.edit', compact('employee','departments','branches', 'roles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeBranchAccess($employee);

        $data = $request->validate([
            'employee_no' => 'required|unique:employees,employee_no,'.$employee->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:employees,email,'.$employee->id,
            'phone' => 'nullable',
            'department_id' => 'nullable|exists:departments,id',
            'branch_id' => 'nullable|exists:branches,id',
            'job_title' => 'nullable',
            'role_name' => ['nullable', 'exists:roles,name', Rule::notIn(['super_admin', 'patient'])],
            'status' => 'required|in:active,on_leave,terminated',
            'hire_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
        ]);
        $data['branch_id'] = $this->resolvedBranchId($data['branch_id'] ?? $employee->branch_id);

        $employee->update($data);
        return redirect()->route('employees.index')->with('success','Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $this->authorizeBranchAccess($employee);

        $employee->delete();
        return redirect()->route('employees.index')->with('success','Employee deleted.');
    }

    private function availableBranches()
    {
        return auth()->user()->isSuperAdmin()
            ? Branch::active()->orderBy('name')->get()
            : Branch::whereKey(auth()->user()->branch_id)->get();
    }

    private function authorizeBranchAccess(Employee $employee): void
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->branch_id === $employee->branch_id, 404);
    }

    private function resolvedBranchId(?int $branchId): ?int
    {
        return auth()->user()->isSuperAdmin() ? $branchId : auth()->user()->branch_id;
    }

    private function employeeRoles()
    {
        return Role::whereNotIn('name', ['super_admin', 'patient'])
            ->orderBy('name')
            ->get();
    }
}

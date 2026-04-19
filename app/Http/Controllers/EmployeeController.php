<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Branch;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['department','branch'])->orderBy('last_name')->paginate(20);
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $departments = Department::all();
        $branches = Branch::all();
        return view('employees.create', compact('departments','branches'));
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
            'status' => 'required|in:active,on_leave,terminated',
            'hire_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
        ]);
        Employee::create($data);
        return redirect()->route('employees.index')->with('success','Employee added.');
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $branches = Branch::all();
        return view('employees.edit', compact('employee','departments','branches'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'employee_no' => 'required|unique:employees,employee_no,'.$employee->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:employees,email,'.$employee->id,
            'phone' => 'nullable',
            'department_id' => 'nullable|exists:departments,id',
            'branch_id' => 'nullable|exists:branches,id',
            'job_title' => 'nullable',
            'status' => 'required|in:active,on_leave,terminated',
            'hire_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
        ]);
        $employee->update($data);
        return redirect()->route('employees.index')->with('success','Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success','Employee deleted.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
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
        $departments = Department::withCount('employees')
            ->orderBy('name')
            ->paginate(20);

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $department = new Department();

        return view('departments.create', compact('department'));
    }

    public function store(Request $request)
    {
        Department::create($this->validatedData($request));

        return redirect()->route('departments.index')->with('success', 'Department added.');
    }

    public function show(Department $department)
    {
        $department->load(['employees.branch']);

        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $department->update($this->validatedData($request, $department));

        return redirect()->route('departments.show', $department)->with('success', 'Department updated.');
    }

    public function destroy(Department $department)
    {
        if ($department->employees()->exists()) {
            return back()->withErrors(['department' => 'This department has employees assigned and cannot be deleted.']);
        }

        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Department deleted.');
    }

    private function validatedData(Request $request, ?Department $department = null): array
    {
        $departmentId = $department?->id ?? 'NULL';

        return $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $departmentId,
            'description' => 'nullable|string|max:500',
        ]);
    }
}

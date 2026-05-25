<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\StaffAppraisal;
use Illuminate\Http\Request;

class StaffAppraisalController extends Controller
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
        $appraisals = StaffAppraisal::visibleTo($request->user())
            ->with(['employee.branch', 'reviewer'])
            ->latest('period_end')
            ->paginate(20);

        return view('appraisals.index', compact('appraisals'));
    }

    public function create(Request $request)
    {
        $employees = Employee::visibleTo($request->user())->orderBy('last_name')->get();
        $appraisal = new StaffAppraisal([
            'period_start' => now()->startOfYear()->toDateString(),
            'period_end' => now()->endOfYear()->toDateString(),
            'status' => 'draft',
            'reviewed_at' => now()->toDateString(),
        ]);

        return view('appraisals.create', compact('appraisal', 'employees'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $employee = Employee::visibleTo($request->user())->findOrFail($data['employee_id']);
        $data['branch_id'] = $employee->branch_id;
        $data['reviewer_id'] = $request->user()->id;

        StaffAppraisal::create($data);

        return redirect()->route('appraisals.index')->with('success', 'Appraisal saved.');
    }

    public function show(Request $request, StaffAppraisal $appraisal)
    {
        $this->authorizeVisible($request, $appraisal);
        $appraisal->load(['employee.branch', 'reviewer']);

        return view('appraisals.show', compact('appraisal'));
    }

    public function edit(Request $request, StaffAppraisal $appraisal)
    {
        $this->authorizeVisible($request, $appraisal);
        $employees = Employee::visibleTo($request->user())->orderBy('last_name')->get();

        return view('appraisals.edit', compact('appraisal', 'employees'));
    }

    public function update(Request $request, StaffAppraisal $appraisal)
    {
        $this->authorizeVisible($request, $appraisal);
        $data = $this->validatedData($request);
        $employee = Employee::visibleTo($request->user())->findOrFail($data['employee_id']);
        $data['branch_id'] = $employee->branch_id;
        $appraisal->update($data);

        return redirect()->route('appraisals.show', $appraisal)->with('success', 'Appraisal updated.');
    }

    public function destroy(Request $request, StaffAppraisal $appraisal)
    {
        $this->authorizeVisible($request, $appraisal);
        $appraisal->delete();

        return redirect()->route('appraisals.index')->with('success', 'Appraisal deleted.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'score' => 'nullable|numeric|min:0|max:100',
            'rating' => 'nullable|string|max:100',
            'strengths' => 'nullable|string',
            'improvement_areas' => 'nullable|string',
            'goals' => 'nullable|string',
            'status' => 'required|in:draft,completed,acknowledged',
            'reviewed_at' => 'nullable|date',
        ]);
    }

    private function authorizeVisible(Request $request, StaffAppraisal $appraisal): void
    {
        abort_unless(StaffAppraisal::visibleTo($request->user())->whereKey($appraisal->id)->exists(), 404);
    }
}

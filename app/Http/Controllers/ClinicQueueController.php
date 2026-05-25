<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClinicQueueController extends Controller
{
    use ResolvesBranchContext;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessAnyModule(['front_office', 'clinic', 'nursing', 'laboratory', 'pharmacy']), 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $branchId = $this->branchFilterId($request);

        $visits = Visit::with(['patient', 'provider', 'vitalSigns'])
            ->visibleTo($request->user(), $branchId)
            ->where('status', 'open')
            ->whereIn('workflow_stage', array_keys(Visit::WORKFLOW_STAGES))
            ->orderBy('visit_date')
            ->get()
            ->groupBy('workflow_stage');

        $stageCounts = collect(Visit::WORKFLOW_STAGES)
            ->mapWithKeys(fn ($label, $stage) => [$stage => $visits->get($stage, collect())->count()]);

        return view('clinic_queue.index', [
            'stages' => Visit::WORKFLOW_STAGES,
            'visitsByStage' => $visits,
            'stageCounts' => $stageCounts,
        ]);
    }

    public function update(Request $request, Visit $visit)
    {
        abort_unless($request->user()->isSuperAdmin() || $request->user()->branch_id === $visit->branch_id, 404);

        $validated = $request->validate([
            'workflow_stage' => ['required', Rule::in(array_keys(Visit::WORKFLOW_STAGES))],
        ]);

        $visit->moveToStage($validated['workflow_stage']);

        if ($visit->appointment && $validated['workflow_stage'] === Visit::STAGE_COMPLETED) {
            $visit->appointment->update(['status' => 'completed']);
        }

        return back()->with('success', 'Visit moved to ' . $visit->fresh()->workflow_stage_label . '.');
    }
}

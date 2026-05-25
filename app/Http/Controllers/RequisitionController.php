<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RequisitionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $requisitions = Requisition::visibleTo($user)
            ->with(['requester', 'lineSupervisor', 'branch'])
            ->latest('requested_at')
            ->paginate(20);
        $scope = 'all';

        return view('requisitions.index', compact('requisitions', 'scope'));
    }

    public function mine(Request $request)
    {
        $requisitions = Requisition::where('requested_by', $request->user()->id)
            ->with(['requester', 'lineSupervisor', 'branch'])
            ->latest('requested_at')
            ->paginate(20);
        $scope = 'mine';

        return view('requisitions.index', compact('requisitions', 'scope'));
    }

    public function create(Request $request)
    {
        $requisition = new Requisition([
            'department' => $request->user()->department,
            'requested_at' => now(),
        ]);

        return view('requisitions.form', compact('requisition'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $this->validatedData($request);
        $status = $request->input('action') === 'submit' ? 'submitted' : 'draft';

        $requisition = DB::transaction(function () use ($data, $user, $status) {
            $requisition = Requisition::create([
                'branch_id' => $user->branch_id,
                'requested_by' => $user->id,
                'line_supervisor_id' => $user->line_supervisor_id,
                'serial_number' => $this->nextSerialNumber(),
                'department' => $data['department'] ?? $user->department,
                'requested_at' => $data['requested_at'],
                'status' => $status,
                'purpose' => $data['purpose'] ?? null,
                'amount_in_words' => $data['amount_in_words'] ?? null,
            ]);

            $this->syncItems($requisition, $data['items'] ?? []);

            return $requisition->fresh();
        });

        AuditLogService::logDataChange($request, 'create', 'requisition', $requisition->id, [], [
            'status' => $requisition->status,
            'total_amount' => $requisition->total_amount,
        ], 'Staff requisition created');

        return redirect()->route('requisitions.show', $requisition)->with('success', 'Requisition saved.');
    }

    public function show(Request $request, Requisition $requisition)
    {
        $this->authorizeVisible($request, $requisition);
        $requisition->load(['items', 'requester', 'lineSupervisor', 'branch', 'checkedBy', 'approvedBy']);
        $logs = AuditLog::byResource('requisition', $requisition->id)
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();

        return view('requisitions.show', compact('requisition', 'logs'));
    }

    public function print(Request $request, Requisition $requisition)
    {
        $this->authorizeVisible($request, $requisition);
        $requisition->load(['items', 'requester', 'lineSupervisor', 'checkedBy', 'approvedBy']);

        return view('requisitions.print', compact('requisition'));
    }

    public function edit(Request $request, Requisition $requisition)
    {
        abort_unless($requisition->canBeEditedBy($request->user()), 403);
        $requisition->load('items');

        return view('requisitions.form', compact('requisition'));
    }

    public function update(Request $request, Requisition $requisition)
    {
        abort_unless($requisition->canBeEditedBy($request->user()), 403);
        $data = $this->validatedData($request);
        $status = $request->input('action') === 'submit' ? 'submitted' : 'draft';

        DB::transaction(function () use ($data, $requisition, $status) {
            $requisition->update([
                'department' => $data['department'] ?? $requisition->department,
                'requested_at' => $data['requested_at'],
                'status' => $status,
                'purpose' => $data['purpose'] ?? null,
                'amount_in_words' => $data['amount_in_words'] ?? null,
            ]);

            $this->syncItems($requisition, $data['items'] ?? []);
        });

        AuditLogService::logDataChange($request, 'update', 'requisition', $requisition->id, [], [
            'status' => $status,
        ], 'Staff requisition updated');

        return redirect()->route('requisitions.show', $requisition)->with('success', 'Requisition updated.');
    }

    public function supervisorReview(Request $request, Requisition $requisition)
    {
        $user = $request->user();
        abort_unless($requisition->line_supervisor_id === $user->id || $user->hasRole(['branch_admin']) || $user->isSuperAdmin(), 403);

        $data = $request->validate([
            'decision' => 'required|in:supervisor_approved,changes_requested,rejected',
            'supervisor_comments' => 'nullable|string',
        ]);

        $requisition->update([
            'status' => $data['decision'],
            'supervisor_comments' => $data['supervisor_comments'] ?? null,
            'supervisor_reviewed_at' => now(),
        ]);

        AuditLogService::logDataChange($request, 'update', 'requisition', $requisition->id, [], [
            'status' => $requisition->status,
        ], 'Supervisor reviewed requisition');

        return back()->with('success', 'Requisition review saved.');
    }

    public function financeReview(Request $request, Requisition $requisition)
    {
        $user = $request->user();
        abort_unless($user->hasRole(['finance_officer', 'branch_admin']) || $user->isSuperAdmin(), 403);

        $data = $request->validate([
            'decision' => 'required|in:finance_checked,approved,rejected',
            'finance_comments' => 'nullable|string',
        ]);

        $requisition->update([
            'status' => $data['decision'],
            'checked_by' => in_array($data['decision'], ['finance_checked', 'approved'], true) ? $user->id : $requisition->checked_by,
            'checked_at' => in_array($data['decision'], ['finance_checked', 'approved'], true) ? now() : $requisition->checked_at,
            'approved_by' => $data['decision'] === 'approved' ? $user->id : $requisition->approved_by,
            'approved_at' => $data['decision'] === 'approved' ? now() : $requisition->approved_at,
            'finance_comments' => $data['finance_comments'] ?? null,
        ]);

        AuditLogService::logDataChange($request, 'update', 'requisition', $requisition->id, [], [
            'status' => $requisition->status,
        ], 'Finance reviewed requisition');

        return back()->with('success', 'Finance decision saved.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'department' => 'nullable|string|max:255',
            'requested_at' => 'required|date',
            'purpose' => 'nullable|string',
            'amount_in_words' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item' => 'required|string|max:255',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.frequency' => 'nullable|string|max:100',
        ]);
    }

    private function syncItems(Requisition $requisition, array $items): void
    {
        $requisition->items()->delete();
        $total = 0;

        foreach ($items as $item) {
            $lineTotal = round(((float) $item['unit_cost']) * ((float) $item['quantity']), 2);
            $total += $lineTotal;
            $requisition->items()->create([
                'item' => $item['item'],
                'unit_cost' => $item['unit_cost'],
                'quantity' => $item['quantity'],
                'frequency' => $item['frequency'] ?? null,
                'total_cost' => $lineTotal,
            ]);
        }

        $requisition->update(['total_amount' => $total]);
    }

    private function nextSerialNumber(): string
    {
        return 'REQ-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
    }

    private function authorizeVisible(Request $request, Requisition $requisition): void
    {
        abort_unless(
            Requisition::visibleTo($request->user())->whereKey($requisition->id)->exists(),
            403
        );
    }
}

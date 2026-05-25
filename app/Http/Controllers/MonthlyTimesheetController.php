<?php

namespace App\Http\Controllers;

use App\Models\MonthlyTimesheet;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonthlyTimesheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $timesheets = MonthlyTimesheet::visibleTo($request->user())
            ->with(['user', 'lineSupervisor', 'branch'])
            ->latest('month')
            ->paginate(20);
        $scope = 'all';

        return view('timesheets.index', compact('timesheets', 'scope'));
    }

    public function mine(Request $request)
    {
        $timesheets = MonthlyTimesheet::where('user_id', $request->user()->id)
            ->with(['user', 'lineSupervisor', 'branch'])
            ->latest('month')
            ->paginate(20);
        $scope = 'mine';

        return view('timesheets.index', compact('timesheets', 'scope'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $timesheet = new MonthlyTimesheet([
            'month' => now()->startOfMonth(),
            'prepared_at' => now(),
            'employee_number' => $user->employee_number,
            'job_title' => $user->job_title,
        ]);
        $entries = collect(range(1, 31))->map(fn ($day) => ['day' => $day]);

        return view('timesheets.form', compact('timesheet', 'entries'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $this->validatedData($request);
        $month = Carbon::parse($data['month'])->startOfMonth();
        $status = $request->input('action') === 'submit' ? 'submitted' : 'draft';

        $timesheet = DB::transaction(function () use ($data, $user, $month, $status) {
            $timesheet = MonthlyTimesheet::create([
                'branch_id' => $user->branch_id,
                'user_id' => $user->id,
                'line_supervisor_id' => $user->line_supervisor_id,
                'employee_number' => $data['employee_number'] ?? $user->employee_number,
                'job_title' => $data['job_title'] ?? $user->job_title,
                'month' => $month,
                'prepared_at' => $data['prepared_at'] ?? now(),
                'status' => $status,
                'staff_comments' => $data['staff_comments'] ?? null,
                'submitted_at' => $status === 'submitted' ? now() : null,
            ]);

            $this->syncEntries($timesheet, $data['entries'] ?? []);

            return $timesheet->fresh();
        });

        AuditLogService::logDataChange($request, 'create', 'monthly_timesheet', $timesheet->id, [], [
            'status' => $timesheet->status,
            'total_hours' => $timesheet->total_hours,
        ], 'Monthly timesheet created');

        return redirect()->route('timesheets.show', $timesheet)->with('success', 'Timesheet saved.');
    }

    public function show(Request $request, MonthlyTimesheet $timesheet)
    {
        $this->authorizeVisible($request, $timesheet);
        $timesheet->load(['entries', 'user', 'lineSupervisor', 'branch', 'hrReceivedBy']);
        $logs = AuditLog::byResource('monthly_timesheet', $timesheet->id)
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();

        return view('timesheets.show', compact('timesheet', 'logs'));
    }

    public function print(Request $request, MonthlyTimesheet $timesheet)
    {
        $this->authorizeVisible($request, $timesheet);
        $timesheet->load(['entries', 'user', 'lineSupervisor', 'hrReceivedBy']);

        return view('timesheets.print', compact('timesheet'));
    }

    public function edit(Request $request, MonthlyTimesheet $timesheet)
    {
        abort_unless($timesheet->canBeEditedBy($request->user()), 403);
        $timesheet->load('entries');
        $existing = $timesheet->entries->keyBy('day');
        $entries = collect(range(1, 31))->map(fn ($day) => $existing->get($day) ?: ['day' => $day]);

        return view('timesheets.form', compact('timesheet', 'entries'));
    }

    public function update(Request $request, MonthlyTimesheet $timesheet)
    {
        abort_unless($timesheet->canBeEditedBy($request->user()), 403);
        $data = $this->validatedData($request);
        $status = $request->input('action') === 'submit' ? 'submitted' : 'draft';

        DB::transaction(function () use ($data, $timesheet, $status) {
            $timesheet->update([
                'employee_number' => $data['employee_number'] ?? $timesheet->employee_number,
                'job_title' => $data['job_title'] ?? $timesheet->job_title,
                'month' => Carbon::parse($data['month'])->startOfMonth(),
                'prepared_at' => $data['prepared_at'] ?? $timesheet->prepared_at,
                'status' => $status,
                'staff_comments' => $data['staff_comments'] ?? null,
                'submitted_at' => $status === 'submitted' ? now() : $timesheet->submitted_at,
            ]);

            $this->syncEntries($timesheet, $data['entries'] ?? []);
        });

        AuditLogService::logDataChange($request, 'update', 'monthly_timesheet', $timesheet->id, [], [
            'status' => $timesheet->status,
        ], 'Monthly timesheet updated');

        return redirect()->route('timesheets.show', $timesheet)->with('success', 'Timesheet updated.');
    }

    public function supervisorReview(Request $request, MonthlyTimesheet $timesheet)
    {
        $user = $request->user();
        abort_unless($timesheet->line_supervisor_id === $user->id || $user->hasRole(['branch_admin']) || $user->isSuperAdmin(), 403);

        $data = $request->validate([
            'decision' => 'required|in:supervisor_approved,changes_requested,rejected',
            'supervisor_comments' => 'nullable|string',
        ]);

        $timesheet->update([
            'status' => $data['decision'],
            'supervisor_comments' => $data['supervisor_comments'] ?? null,
            'supervisor_reviewed_at' => now(),
        ]);

        AuditLogService::logDataChange($request, 'update', 'monthly_timesheet', $timesheet->id, [], [
            'status' => $timesheet->status,
        ], 'Supervisor reviewed monthly timesheet');

        return back()->with('success', 'Timesheet review saved.');
    }

    public function hrReceive(Request $request, MonthlyTimesheet $timesheet)
    {
        $user = $request->user();
        abort_unless($user->hasRole(['hr_manager', 'branch_admin']) || $user->isSuperAdmin(), 403);

        $data = $request->validate([
            'hr_comments' => 'nullable|string',
        ]);

        $timesheet->update([
            'status' => 'hr_received',
            'hr_received_by' => $user->id,
            'hr_received_at' => now(),
            'hr_comments' => $data['hr_comments'] ?? null,
        ]);

        AuditLogService::logDataChange($request, 'update', 'monthly_timesheet', $timesheet->id, [], [
            'status' => $timesheet->status,
        ], 'HR received monthly timesheet');

        return back()->with('success', 'Timesheet marked as received by HR.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'employee_number' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:255',
            'month' => 'required|date',
            'prepared_at' => 'nullable|date',
            'staff_comments' => 'nullable|string',
            'entries' => 'required|array|size:31',
            'entries.*.day' => 'required|integer|min:1|max:31',
            'entries.*.work_specification' => 'nullable|string',
            'entries.*.time_start' => 'nullable|date_format:H:i',
            'entries.*.time_finish' => 'nullable|date_format:H:i',
        ]);
    }

    private function syncEntries(MonthlyTimesheet $timesheet, array $entries): void
    {
        $timesheet->entries()->delete();
        $totalHours = 0;

        foreach ($entries as $entry) {
            $hours = $this->calculateHours($entry['time_start'] ?? null, $entry['time_finish'] ?? null);
            $totalHours += $hours;
            $timesheet->entries()->create([
                'day' => $entry['day'],
                'work_specification' => $entry['work_specification'] ?? null,
                'time_start' => $entry['time_start'] ?? null,
                'time_finish' => $entry['time_finish'] ?? null,
                'hours' => $hours,
            ]);
        }

        $timesheet->update(['total_hours' => $totalHours]);
    }

    private function calculateHours(?string $start, ?string $finish): float
    {
        if (! $start || ! $finish) {
            return 0;
        }

        $startTime = Carbon::createFromFormat('H:i', $start);
        $finishTime = Carbon::createFromFormat('H:i', $finish);

        if ($finishTime->lessThan($startTime)) {
            $finishTime->addDay();
        }

        return round($startTime->diffInMinutes($finishTime) / 60, 2);
    }

    private function authorizeVisible(Request $request, MonthlyTimesheet $timesheet): void
    {
        abort_unless(
            MonthlyTimesheet::visibleTo($request->user())->whereKey($timesheet->id)->exists(),
            403
        );
    }
}

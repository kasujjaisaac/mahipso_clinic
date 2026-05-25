@extends('layouts.app')

@section('title', 'Monthly Timesheet')
@section('page_title', 'Timesheet - ' . $timesheet->month->format('F Y'))
@section('page_subtitle', 'Review monthly work entries, total hours, and approval status.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('timesheets.index') }}">Back to timesheets</a>
    <a class="ghost-button" href="{{ route('timesheets.print', $timesheet) }}" target="_blank">Preview / print</a>
    @if($timesheet->canBeEditedBy(auth()->user()))
        <a class="primary-button" href="{{ route('timesheets.edit', $timesheet) }}">Edit</a>
    @endif
@endsection

@section('content')
    <div class="panel">
        <div class="detail-grid">
            <div class="detail-item"><span class="detail-label">Name</span><div class="detail-value">{{ $timesheet->user->name ?? '-' }}</div></div>
            <div class="detail-item"><span class="detail-label">Month</span><div class="detail-value">{{ $timesheet->month->format('F Y') }}</div></div>
            <div class="detail-item"><span class="detail-label">Job title</span><div class="detail-value">{{ $timesheet->job_title ?? '-' }}</div></div>
            <div class="detail-item"><span class="detail-label">Employee number</span><div class="detail-value">{{ $timesheet->employee_number ?? '-' }}</div></div>
            <div class="detail-item"><span class="detail-label">Supervisor</span><div class="detail-value">{{ $timesheet->lineSupervisor->name ?? 'Not assigned' }}</div></div>
            <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value">{{ ucfirst(str_replace('_', ' ', $timesheet->status)) }}</div></div>
        </div>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Day</th><th>Specification of work</th><th>Time start</th><th>Time finish</th><th>Hours</th></tr></thead>
                <tbody>
                    @foreach($timesheet->entries->sortBy('day') as $entry)
                        <tr>
                            <td>{{ $entry->day }}</td>
                            <td>{{ $entry->work_specification ?? '-' }}</td>
                            <td>{{ $entry->time_start ?? '-' }}</td>
                            <td>{{ $entry->time_finish ?? '-' }}</td>
                            <td>{{ number_format($entry->hours, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot><tr><th colspan="4">Total hours worked</th><th>{{ number_format($timesheet->total_hours, 2) }}</th></tr></tfoot>
            </table>
        </div>
    </div>

    @if($timesheet->staff_comments || $timesheet->supervisor_comments || $timesheet->hr_comments)
        <div class="panel">
            <div class="detail-grid">
                <div class="detail-item"><span class="detail-label">Staff comments</span><div class="detail-value">{{ $timesheet->staff_comments ?? '-' }}</div></div>
                <div class="detail-item"><span class="detail-label">Supervisor comments</span><div class="detail-value">{{ $timesheet->supervisor_comments ?? '-' }}</div></div>
                <div class="detail-item"><span class="detail-label">HR comments</span><div class="detail-value">{{ $timesheet->hr_comments ?? '-' }}</div></div>
            </div>
        </div>
    @endif

    <div class="panel">
        <h2 class="section-title">Timesheet log</h2>
        <div class="table-wrap" style="margin-top: 0.65rem;">
            <table>
                <thead><tr><th>Date</th><th>User</th><th>Action</th><th>Details</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $log->user->name ?? 'System' }}</td>
                            <td>{{ ucfirst($log->action_type ?? $log->action) }}</td>
                            <td>
                                {{ $log->description }}
                                @if($log->changes_summary)
                                    <div class="subtle">{{ $log->changes_summary }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">No log entries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(($timesheet->line_supervisor_id === auth()->id() || auth()->user()->hasRole(['branch_admin']) || auth()->user()->isSuperAdmin()) && $timesheet->status === 'submitted')
        <div class="panel">
            <h2 class="section-title">Supervisor review</h2>
            <form method="POST" action="{{ route('timesheets.supervisor-review', $timesheet) }}" class="form-grid" style="margin-top: 0.65rem;">
                @csrf
                <div class="field">
                    <label>Decision</label>
                    <select name="decision" required>
                        <option value="supervisor_approved">Approve</option>
                        <option value="changes_requested">Request changes</option>
                        <option value="rejected">Reject</option>
                    </select>
                </div>
                <div class="field field-span-2">
                    <label>Comments</label>
                    <textarea name="supervisor_comments"></textarea>
                </div>
                <div><button class="primary-button" type="submit">Save review</button></div>
            </form>
        </div>
    @endif

    @if((auth()->user()->hasRole(['hr_manager', 'branch_admin']) || auth()->user()->isSuperAdmin()) && $timesheet->status === 'supervisor_approved')
        <div class="panel">
            <h2 class="section-title">HRM receipt</h2>
            <form method="POST" action="{{ route('timesheets.hr-receive', $timesheet) }}" class="form-grid" style="margin-top: 0.65rem;">
                @csrf
                <div class="field field-span-2">
                    <label>HR comments</label>
                    <textarea name="hr_comments"></textarea>
                </div>
                <div><button class="primary-button" type="submit">Mark received</button></div>
            </form>
        </div>
    @endif
@endsection

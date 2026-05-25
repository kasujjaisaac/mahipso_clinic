@extends('layouts.app')

@section('title', 'Requisition')
@section('page_title', 'Requisition ' . $requisition->serial_number)
@section('page_subtitle', 'Review requisition details, supervisor decision, and finance approval.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('requisitions.index') }}">Back to requisitions</a>
    <a class="ghost-button" href="{{ route('requisitions.print', $requisition) }}" target="_blank">Preview / print</a>
    @if($requisition->canBeEditedBy(auth()->user()))
        <a class="primary-button" href="{{ route('requisitions.edit', $requisition) }}">Edit</a>
    @endif
@endsection

@section('content')
    <div class="panel">
        <div class="detail-grid">
            <div class="detail-item"><span class="detail-label">Serial No.</span><div class="detail-value">{{ $requisition->serial_number }}</div></div>
            <div class="detail-item"><span class="detail-label">From</span><div class="detail-value">{{ $requisition->requester->name ?? '-' }}</div></div>
            <div class="detail-item"><span class="detail-label">Department</span><div class="detail-value">{{ $requisition->department ?? '-' }}</div></div>
            <div class="detail-item"><span class="detail-label">Date</span><div class="detail-value">{{ $requisition->requested_at->format('Y-m-d') }}</div></div>
            <div class="detail-item"><span class="detail-label">Supervisor</span><div class="detail-value">{{ $requisition->lineSupervisor->name ?? 'Not assigned' }}</div></div>
            <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value">{{ ucfirst(str_replace('_', ' ', $requisition->status)) }}</div></div>
        </div>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>No.</th><th>Item</th><th>Unit cost</th><th>Quantity</th><th>Freq</th><th>Total cost</th></tr></thead>
                <tbody>
                    @foreach($requisition->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->item }}</td>
                            <td>{{ number_format($item->unit_cost, 2) }}</td>
                            <td>{{ number_format($item->quantity, 2) }}</td>
                            <td>{{ $item->frequency ?? '-' }}</td>
                            <td>{{ number_format($item->total_cost, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot><tr><th colspan="5">Total</th><th>{{ number_format($requisition->total_amount, 2) }}</th></tr></tfoot>
            </table>
        </div>
        <div class="detail-item" style="margin-top: 0.65rem;">
            <span class="detail-label">Total amount in words</span>
            <div class="detail-value">{{ $requisition->amount_in_words ?? '-' }}</div>
        </div>
    </div>

    @if($requisition->purpose || $requisition->supervisor_comments || $requisition->finance_comments)
        <div class="panel">
            <div class="detail-grid">
                <div class="detail-item"><span class="detail-label">Purpose</span><div class="detail-value">{{ $requisition->purpose ?? '-' }}</div></div>
                <div class="detail-item"><span class="detail-label">Supervisor comments</span><div class="detail-value">{{ $requisition->supervisor_comments ?? '-' }}</div></div>
                <div class="detail-item"><span class="detail-label">Finance comments</span><div class="detail-value">{{ $requisition->finance_comments ?? '-' }}</div></div>
            </div>
        </div>
    @endif

    <div class="panel">
        <h2 class="section-title">Requisition log</h2>
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

    @if(($requisition->line_supervisor_id === auth()->id() || auth()->user()->hasRole(['branch_admin']) || auth()->user()->isSuperAdmin()) && $requisition->status === 'submitted')
        <div class="panel">
            <h2 class="section-title">Supervisor review</h2>
            <form method="POST" action="{{ route('requisitions.supervisor-review', $requisition) }}" class="form-grid" style="margin-top: 0.65rem;">
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

    @if((auth()->user()->hasRole(['finance_officer', 'branch_admin']) || auth()->user()->isSuperAdmin()) && in_array($requisition->status, ['supervisor_approved', 'finance_checked'], true))
        <div class="panel">
            <h2 class="section-title">Finance approval</h2>
            <form method="POST" action="{{ route('requisitions.finance-review', $requisition) }}" class="form-grid" style="margin-top: 0.65rem;">
                @csrf
                <div class="field">
                    <label>Decision</label>
                    <select name="decision" required>
                        <option value="finance_checked">Checked</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Reject</option>
                    </select>
                </div>
                <div class="field field-span-2">
                    <label>Comments</label>
                    <textarea name="finance_comments"></textarea>
                </div>
                <div><button class="primary-button" type="submit">Save finance decision</button></div>
            </form>
        </div>
    @endif
@endsection

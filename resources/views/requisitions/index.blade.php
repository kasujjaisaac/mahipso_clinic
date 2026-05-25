@extends('layouts.app')

@section('title', $scope === 'mine' ? 'My Requisitions' : 'Requisitions')
@section('page_title', $scope === 'mine' ? 'My requisitions' : 'Requisitions')
@section('page_subtitle', $scope === 'mine' ? 'Track your drafts, submitted requisitions, approvals, and rejected requests.' : 'Create, submit, and review staff requisitions.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('requisitions.create') }}">New requisition</a>
@endsection

@section('content')
    @php
        $statusLabels = [
            'draft' => 'Draft',
            'submitted' => 'Waiting for supervisor',
            'supervisor_approved' => 'Supervisor approved',
            'changes_requested' => 'Changes requested',
            'rejected' => 'Rejected',
            'finance_checked' => 'Finance checked',
            'approved' => 'Approved',
        ];
    @endphp

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Serial</th>
                        <th>Requester</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requisitions as $requisition)
                        <tr>
                            <td>{{ $requisition->serial_number }}</td>
                            <td>{{ $requisition->requester->name ?? '-' }}</td>
                            <td>{{ $requisition->department ?? '-' }}</td>
                            <td>{{ $requisition->requested_at->format('Y-m-d') }}</td>
                            <td><span class="status-pill {{ $requisition->status }}">{{ ucfirst(str_replace('_', ' ', $requisition->status)) }}</span></td>
                            <td>
                                {{ $statusLabels[$requisition->status] ?? ucfirst(str_replace('_', ' ', $requisition->status)) }}
                                @if($requisition->status === 'changes_requested' && $requisition->supervisor_comments)
                                    <div class="subtle">{{ $requisition->supervisor_comments }}</div>
                                @elseif($requisition->status === 'rejected')
                                    <div class="subtle">{{ $requisition->supervisor_comments ?: $requisition->finance_comments }}</div>
                                @endif
                            </td>
                            <td>{{ number_format($requisition->total_amount, 2) }}</td>
                            <td><a class="badge-link" href="{{ route('requisitions.show', $requisition) }}">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">No requisitions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $requisitions->links() }}</div>
    </div>
@endsection

@extends('layouts.app')

@section('title', $scope === 'mine' ? 'My Timesheets' : 'Monthly Timesheets')
@section('page_title', $scope === 'mine' ? 'My timesheets' : 'Monthly timesheets')
@section('page_subtitle', $scope === 'mine' ? 'Track your monthly draft, submitted, approved, rejected, and HR-received timesheets.' : 'Create, submit, and review MAHIPSO monthly staff timesheets.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('timesheets.create') }}">New timesheet</a>
@endsection

@section('content')
    @php
        $statusLabels = [
            'draft' => 'Draft',
            'submitted' => 'Waiting for supervisor',
            'supervisor_approved' => 'Supervisor approved',
            'changes_requested' => 'Changes requested',
            'rejected' => 'Rejected',
            'hr_received' => 'Received by HR',
        ];
    @endphp

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Staff</th>
                        <th>Job title</th>
                        <th>Supervisor</th>
                        <th>Status</th>
                        <th>Progress</th>
                        <th>Total hours</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($timesheets as $timesheet)
                        <tr>
                            <td>{{ $timesheet->month->format('F Y') }}</td>
                            <td>{{ $timesheet->user->name ?? '-' }}</td>
                            <td>{{ $timesheet->job_title ?? '-' }}</td>
                            <td>{{ $timesheet->lineSupervisor->name ?? 'Not assigned' }}</td>
                            <td><span class="status-pill {{ $timesheet->status }}">{{ ucfirst(str_replace('_', ' ', $timesheet->status)) }}</span></td>
                            <td>
                                {{ $statusLabels[$timesheet->status] ?? ucfirst(str_replace('_', ' ', $timesheet->status)) }}
                                @if($timesheet->status === 'changes_requested' && $timesheet->supervisor_comments)
                                    <div class="subtle">{{ $timesheet->supervisor_comments }}</div>
                                @elseif($timesheet->status === 'rejected')
                                    <div class="subtle">{{ $timesheet->supervisor_comments }}</div>
                                @endif
                            </td>
                            <td>{{ number_format($timesheet->total_hours, 2) }}</td>
                            <td><a class="badge-link" href="{{ route('timesheets.show', $timesheet) }}">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty-state">No timesheets yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $timesheets->links() }}</div>
    </div>
@endsection

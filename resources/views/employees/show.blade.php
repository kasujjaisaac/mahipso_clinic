@extends('layouts.app')

@section('title', 'Employee Profile')
@section('section', 'HR Management')
@section('kicker', 'Employee Registry')
@section('page_title', 'Employee Profile')
@section('page_subtitle', $employee->first_name . ' ' . $employee->last_name)

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('employees.index') }}">Back to Employees</a>
    <a class="primary-button" href="{{ route('employees.edit', $employee) }}">Edit</a>
@endsection

@section('content')
<div class="panel">
    <div class="detail-grid">
        <div class="detail-item"><span class="detail-label">Employee No</span><div class="detail-value">{{ $employee->employee_no }}</div></div>
        <div class="detail-item"><span class="detail-label">Name</span><div class="detail-value">{{ $employee->first_name }} {{ $employee->last_name }}</div></div>
        <div class="detail-item"><span class="detail-label">Email</span><div class="detail-value">{{ $employee->email }}</div></div>
        <div class="detail-item"><span class="detail-label">Phone</span><div class="detail-value">{{ $employee->phone }}</div></div>
        <div class="detail-item"><span class="detail-label">Department</span><div class="detail-value">{{ optional($employee->department)->name }}</div></div>
        <div class="detail-item"><span class="detail-label">Branch</span><div class="detail-value">{{ optional($employee->branch)->name }}</div></div>
        <div class="detail-item"><span class="detail-label">Job Title</span><div class="detail-value">{{ $employee->job_title }}</div></div>
        <div class="detail-item"><span class="detail-label">System Role</span><div class="detail-value">{{ $employee->role_name ? ucfirst(str_replace('_', ' ', $employee->role_name)) : 'Not assigned' }}</div></div>
        <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value">{{ ucfirst($employee->status) }}</div></div>
        <div class="detail-item"><span class="detail-label">Hire Date</span><div class="detail-value">{{ $employee->hire_date }}</div></div>
        <div class="detail-item"><span class="detail-label">Termination Date</span><div class="detail-value">{{ $employee->termination_date }}</div></div>
    </div>
</div>

<div class="panel">
    <h2 class="section-title">Contracts</h2>
    <div class="table-wrap" style="margin-top: 0.65rem;">
        <table>
            <thead><tr><th>Contract No</th><th>Type</th><th>Start</th><th>End</th><th>Salary</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($employee->contracts as $contract)
                    <tr>
                        <td><a class="badge-link" href="{{ route('contracts.show', $contract) }}">{{ $contract->contract_no }}</a></td>
                        <td>{{ ucfirst(str_replace('_', ' ', $contract->contract_type)) }}</td>
                        <td>{{ $contract->start_date?->format('Y-m-d') }}</td>
                        <td>{{ $contract->end_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ number_format($contract->salary_amount, 2) }}</td>
                        <td>{{ ucfirst($contract->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">No contracts recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="panel">
    <h2 class="section-title">Appraisals</h2>
    <div class="table-wrap" style="margin-top: 0.65rem;">
        <table>
            <thead><tr><th>Period</th><th>Score</th><th>Rating</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($employee->appraisals as $appraisal)
                    <tr>
                        <td>{{ $appraisal->period_start?->format('Y-m-d') }} to {{ $appraisal->period_end?->format('Y-m-d') }}</td>
                        <td>{{ $appraisal->score !== null ? number_format($appraisal->score, 2) : '-' }}</td>
                        <td>{{ $appraisal->rating ?? '-' }}</td>
                        <td>{{ ucfirst($appraisal->status) }}</td>
                        <td><a class="badge-link" href="{{ route('appraisals.show', $appraisal) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No appraisals recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

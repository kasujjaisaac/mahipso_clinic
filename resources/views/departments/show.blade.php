@extends('layouts.app')

@section('title', 'Department')
@section('page_title', $department->name)
@section('page_subtitle', 'Department profile and assigned employees.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('departments.index') }}">Back to departments</a>
    <a class="primary-button" href="{{ route('departments.edit', $department) }}">Edit</a>
@endsection

@section('content')
<div class="panel">
    <div class="detail-grid">
        <div class="detail-item"><span class="detail-label">Name</span><div class="detail-value">{{ $department->name }}</div></div>
        <div class="detail-item"><span class="detail-label">Employees</span><div class="detail-value">{{ $department->employees->count() }}</div></div>
        <div class="detail-item"><span class="detail-label">Description</span><div class="detail-value">{{ $department->description ?? '-' }}</div></div>
    </div>
</div>

<div class="panel">
    <h2 class="section-title">Assigned employees</h2>
    <div class="table-wrap" style="margin-top: 0.65rem;">
        <table>
            <thead><tr><th>Employee No</th><th>Name</th><th>Job Title</th><th>Branch</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($department->employees as $employee)
                    <tr>
                        <td>{{ $employee->employee_no }}</td>
                        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        <td>{{ $employee->job_title ?? '-' }}</td>
                        <td>{{ $employee->branch->name ?? '-' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $employee->status)) }}</td>
                        <td><a class="badge-link" href="{{ route('employees.show', $employee) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">No employees assigned to this department.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

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
        <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value">{{ ucfirst($employee->status) }}</div></div>
        <div class="detail-item"><span class="detail-label">Hire Date</span><div class="detail-value">{{ $employee->hire_date }}</div></div>
        <div class="detail-item"><span class="detail-label">Termination Date</span><div class="detail-value">{{ $employee->termination_date }}</div></div>
    </div>
</div>
@endsection

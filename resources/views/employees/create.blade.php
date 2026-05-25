@extends('layouts.app')

@section('title', 'Add Employee')
@section('section', 'HR Management')
@section('kicker', 'Employee Registry')
@section('page_title', 'Add Employee')
@section('page_subtitle', 'Register a new clinic staff member.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('employees.index') }}">Back to Employees</a>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('employees.store') }}">
        @csrf
        <div class="form-grid">
            <div class="field">
                <label for="employee_no">Employee No</label>
                <input id="employee_no" name="employee_no" value="{{ old('employee_no') }}" required>
            </div>
            <div class="field">
                <label for="first_name">First Name</label>
                <input id="first_name" name="first_name" value="{{ old('first_name') }}" required>
            </div>
            <div class="field">
                <label for="last_name">Last Name</label>
                <input id="last_name" name="last_name" value="{{ old('last_name') }}" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
            </div>
            <div class="field">
                <label for="phone">Phone</label>
                <input id="phone" name="phone" value="{{ old('phone') }}">
            </div>
            <div class="field">
                <label for="department_id">Department</label>
                <select id="department_id" name="department_id">
                    <option value="">Unassigned</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="branch_id">Branch</label>
                <select id="branch_id" name="branch_id">
                    <option value="">Unassigned</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="job_title">Job Title</label>
                <input id="job_title" name="job_title" value="{{ old('job_title') }}">
            </div>
            <div class="field">
                <label for="role_name">System Role</label>
                <select id="role_name" name="role_name">
                    <option value="">Not assigned</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role_name') === $role->name ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="active">Active</option>
                    <option value="on_leave">On Leave</option>
                    <option value="terminated">Terminated</option>
                </select>
            </div>
            <div class="field">
                <label for="hire_date">Hire Date</label>
                <input id="hire_date" name="hire_date" type="date" value="{{ old('hire_date') }}">
            </div>
            <div class="field">
                <label for="termination_date">Termination Date</label>
                <input id="termination_date" name="termination_date" type="date" value="{{ old('termination_date') }}">
            </div>
        </div>
        <button class="primary-button" type="submit">Add Employee</button>
    </form>
</div>
@endsection

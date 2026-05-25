@extends('layouts.app')

@section('title', 'Edit User')
@section('section', 'User Management')
@section('kicker', 'Admin user management')
@section('page_title', 'Edit user')
@section('page_subtitle', 'Update staff account details and branch assignment.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('users.index') }}">Back to users</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('users.update', $user) }}" class="form-grid">
            @csrf
            @method('PUT')

            <div>
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name')<p class="subtle">{{ $message }}</p>@enderror
            </div>

            <div>
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email')<p class="subtle">{{ $message }}</p>@enderror
            </div>

            <div>
                <label>Password (leave blank to keep current)</label>
                <input type="password" name="password">
                @error('password')<p class="subtle">{{ $message }}</p>@enderror
            </div>

            <div>
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation">
            </div>

            <div>
                <label>Role</label>
                <select name="role" required>
                    <option value="">Select role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $userRole) === $role->name ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                    @endforeach
                </select>
                @error('role')<p class="subtle">{{ $message }}</p>@enderror
            </div>

            <div>
                <label>Branch</label>
                <select name="branch_id">
                    <option value="">Global (only super admin)</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')<p class="subtle">{{ $message }}</p>@enderror
            </div>

            <div>
                <label>Employee number</label>
                <input type="text" name="employee_number" value="{{ old('employee_number', $user->employee_number) }}">
                @error('employee_number')<p class="subtle">{{ $message }}</p>@enderror
            </div>

            <div>
                <label>Job title</label>
                <input type="text" name="job_title" value="{{ old('job_title', $user->job_title) }}">
                @error('job_title')<p class="subtle">{{ $message }}</p>@enderror
            </div>

            <div>
                <label>Department</label>
                <input type="text" name="department" value="{{ old('department', $user->department) }}">
                @error('department')<p class="subtle">{{ $message }}</p>@enderror
            </div>

            <div>
                <label>Line supervisor</label>
                <select name="line_supervisor_id">
                    <option value="">Not assigned</option>
                    @foreach($supervisors as $supervisor)
                        <option value="{{ $supervisor->id }}" {{ old('line_supervisor_id', $user->line_supervisor_id) == $supervisor->id ? 'selected' : '' }}>{{ $supervisor->name }}</option>
                    @endforeach
                </select>
                @error('line_supervisor_id')<p class="subtle">{{ $message }}</p>@enderror
            </div>

            <div>
                <button class="primary-button" type="submit">Update user</button>
            </div>
        </form>
    </div>
@endsection

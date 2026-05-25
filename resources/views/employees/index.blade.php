@extends('layouts.app')

@section('title', 'Employees')
@section('section', 'HR Management')
@section('kicker', 'Employee Registry')
@section('page_title', 'Employees')
@section('page_subtitle', 'Manage all clinic staff and employment records.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('employees.create') }}">Add Employee</a>
@endsection

@section('content')
<div class="panel">
    <table>
        <thead>
            <tr>
                <th>Emp. No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Department</th>
                <th>Branch</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $employee->employee_no }}</td>
                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->role_name ? ucfirst(str_replace('_', ' ', $employee->role_name)) : '-' }}</td>
                    <td>{{ optional($employee->department)->name }}</td>
                    <td>{{ optional($employee->branch)->name }}</td>
                    <td>{{ ucfirst($employee->status) }}</td>
                    <td>
                        <a class="ghost-button" href="{{ route('employees.show', $employee) }}">View</a>
                        <a class="primary-button" href="{{ route('employees.edit', $employee) }}">Edit</a>
                        <form method="POST" action="{{ route('employees.destroy', $employee) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="danger-button" type="submit" onclick="return confirm('Delete employee?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination-wrap">{{ $employees->links() }}</div>
</div>
@endsection

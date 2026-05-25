@extends('layouts.app')

@section('title', 'Departments')
@section('page_title', 'Departments')
@section('page_subtitle', 'Manage staff departments used on employee profiles and HR reports.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('departments.create') }}">New department</a>
@endsection

@section('content')
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Employees</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $department)
                    <tr>
                        <td>{{ $department->name }}</td>
                        <td>{{ $department->description ?? '-' }}</td>
                        <td>{{ $department->employees_count }}</td>
                        <td><a class="badge-link" href="{{ route('departments.show', $department) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No departments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $departments->links() }}</div>
</div>
@endsection

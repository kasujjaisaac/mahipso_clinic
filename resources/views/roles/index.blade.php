@extends('layouts.app')

@section('title', 'Roles & Access')
@section('page_title', 'Roles & Access')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('roles.create') }}">Add role</a>
@endsection

@section('content')
<div class="panel">
    <div class="panel-header">
        <h2 class="section-title">Role access modules</h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Description</th>
                    <th>Office Modules</th>
                    <th>Users</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    @php($selected = $role->allowedModules())
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</td>
                        <td>{{ $role->description ?: 'No description.' }}</td>
                        <td>
                            @forelse($selected as $module)
                                <span class="chip">{{ $modules[$module]['label'] ?? ucfirst(str_replace('_', ' ', $module)) }}</span>
                            @empty
                                <span class="subtle">No modules assigned.</span>
                            @endforelse
                        </td>
                        <td>{{ $role->users->count() }}</td>
                        <td>
                            <div class="inline-actions">
                                <a class="ghost-button" href="{{ route('roles.show', $role) }}">View</a>
                                <a class="ghost-button" href="{{ route('roles.edit', $role) }}">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No roles found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $roles->links() }}</div>
</div>
@endsection

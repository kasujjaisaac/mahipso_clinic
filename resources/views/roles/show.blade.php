@extends('layouts.app')

@section('title', 'Role Details')
@section('page_title', ucfirst(str_replace('_', ' ', $role->name)))

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('roles.edit', $role) }}">Edit access</a>
    <a class="ghost-button" href="{{ route('roles.index') }}">Roles</a>
@endsection

@section('content')
<div class="panel">
    <div class="detail-grid">
        <div class="detail-item">
            <span class="detail-label">Role</span>
            <div class="detail-value">{{ $role->name }}</div>
        </div>
        <div class="detail-item">
            <span class="detail-label">Users</span>
            <div class="detail-value">{{ $role->users->count() }}</div>
        </div>
        <div class="detail-item field-span-2">
            <span class="detail-label">Description</span>
            <div class="detail-value">{{ $role->description ?: 'No description recorded.' }}</div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2 class="section-title">Allowed Office Modules</h2>
    </div>
    <div class="card-grid">
        @forelse($selectedModules as $moduleKey)
            <div class="info-card">
                <h3>{{ $modules[$moduleKey]['label'] ?? ucfirst(str_replace('_', ' ', $moduleKey)) }}</h3>
                <p>{{ $modules[$moduleKey]['description'] ?? '' }}</p>
            </div>
        @empty
            <div class="empty-state">No modules assigned to this role.</div>
        @endforelse
    </div>
</div>

<div class="panel">
    <div class="panel-header">
        <h2 class="section-title">Users With This Role</h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Email</th><th>Branch</th></tr></thead>
            <tbody>
                @forelse($role->users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->branch->name ?? 'Global' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty-state">No users assigned.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

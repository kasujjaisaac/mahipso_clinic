@extends('layouts.app')

@section('title', 'Users')
@section('section', 'User Management')
@section('kicker', 'Admin user management')
@section('page_title', 'Users')
@section('page_subtitle', 'Create and manage clinic staff accounts for doctors, nurses, and branch admins.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('users.create') }}">New user</a>
@endsection

@section('content')
    <div class="panel">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Users</h2>
                <p class="table-meta">Super admin can create, edit, and assign roles/branches.</p>
            </div>
            <form class="toolbar-form" method="GET" action="{{ route('users.index') }}">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users">
                <button class="ghost-button" type="submit">Search</button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Branch</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->getRoleNames()->first() ?: 'None' }}</td>
                            <td>{{ optional($user->branch)->name ?? 'Global' }}</td>
                            <td>
                                <div class="inline-actions">
                                    <a class="ghost-button" href="{{ route('users.edit', $user) }}">Edit</a>
                                    @if(auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="danger-button" type="submit">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $users->links() }}</div>
    </div>
@endsection
@extends('layouts.app')

@section('title', 'User Details')
@section('section', 'User Management')
@section('kicker', 'Admin user management')
@section('page_title', 'User details')
@section('page_subtitle', 'Review user account information.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('users.index') }}">Back to users</a>
@endsection

@section('content')
    <div class="panel">
        <div class="detail-grid">
            <div class="panel">
                <h3>Name</h3>
                <p>{{ $user->name }}</p>
            </div>

            <div class="panel">
                <h3>Email</h3>
                <p>{{ $user->email }}</p>
            </div>

            <div class="panel">
                <h3>Role</h3>
                <p>{{ $user->getRoleNames()->first() ?: 'None' }}</p>
            </div>

            <div class="panel">
                <h3>Branch</h3>
                <p>{{ optional($user->branch)->name ?? 'Global' }}</p>
            </div>

            <div class="panel">
                <h3>Created</h3>
                <p>{{ $user->created_at->format('Y-m-d H:i') }}</p>
            </div>

            <div class="panel">
                <h3>Last updated</h3>
                <p>{{ $user->updated_at->format('Y-m-d H:i') }}</p>
            </div>
        </div>
    </div>
@endsection
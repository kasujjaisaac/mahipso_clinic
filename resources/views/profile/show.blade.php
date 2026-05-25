@extends('layouts.app')

@section('title', 'My Profile')
@section('page_title', 'My profile')
@section('page_subtitle', 'Review your account details and clinic access.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('profile.edit') }}">Edit profile</a>
@endsection

@section('content')
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Account information</h2>
                <p class="subtle">These details are used for login, audit trails, and staff identification.</p>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Name</span>
                <div class="detail-value">{{ $user->name }}</div>
            </div>

            <div class="detail-item">
                <span class="detail-label">Email</span>
                <div class="detail-value">{{ $user->email }}</div>
            </div>

            <div class="detail-item">
                <span class="detail-label">Role</span>
                <div class="detail-value">{{ $user->getRoleNames()->map(fn ($role) => ucfirst(str_replace('_', ' ', $role)))->implode(', ') ?: 'None assigned' }}</div>
            </div>

            <div class="detail-item">
                <span class="detail-label">Branch</span>
                <div class="detail-value">{{ optional($user->branch)->name ?? 'Global access' }}</div>
            </div>

            <div class="detail-item">
                <span class="detail-label">Password changed</span>
                <div class="detail-value">{{ optional($user->last_password_changed_at)->format('Y-m-d H:i') ?? 'Not recorded' }}</div>
            </div>

            <div class="detail-item">
                <span class="detail-label">Account created</span>
                <div class="detail-value">{{ $user->created_at->format('Y-m-d H:i') }}</div>
            </div>
        </div>
    </div>
@endsection

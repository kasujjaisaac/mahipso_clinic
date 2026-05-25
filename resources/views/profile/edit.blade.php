@extends('layouts.app')

@section('title', 'Edit Profile')
@section('page_title', 'Edit profile')
@section('page_subtitle', 'Update your account details and password.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ $user->isSuperAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}">Back to dashboard</a>
@endsection

@section('content')
    @php
        $nameParts = collect(explode(' ', trim($user->name)))->filter()->values();
        $initials = $nameParts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
        $roleNames = $user->getRoleNames()
            ->map(fn ($role) => ucfirst(str_replace('_', ' ', $role)))
            ->implode(', ');
    @endphp

    <style>
        .profile-page {
            display: grid;
            grid-template-columns: minmax(220px, 300px) minmax(0, 1fr);
            gap: 0.85rem;
            align-items: start;
        }

        .profile-summary {
            position: sticky;
            top: 0.85rem;
        }

        .profile-identity {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr);
            gap: 0.7rem;
            align-items: center;
            margin-bottom: 0.8rem;
        }

        .profile-avatar {
            width: 58px;
            height: 58px;
            border: 1px solid var(--line);
            border-radius: 4px;
            display: grid;
            place-items: center;
            background: #fff7f7;
            color: var(--brand);
            font-size: 1.1rem;
            font-weight: 700;
        }

        .profile-identity h2 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }

        .profile-identity p,
        .profile-note {
            margin: 0.16rem 0 0;
            color: var(--muted);
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        .profile-stack {
            display: grid;
            gap: 0.85rem;
        }

        .profile-section {
            padding: 0.8rem;
            border: 1px solid var(--line);
            background: #ffffff;
            box-shadow: var(--shadow);
        }

        .profile-section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.6rem;
            margin-bottom: 0.75rem;
        }

        .profile-section-header h3 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .profile-section-header p {
            margin: 0.15rem 0 0;
            color: var(--muted);
            line-height: 1.45;
        }

        .profile-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.7rem;
        }

        .profile-form-grid .field-wide {
            grid-column: 1 / -1;
        }

        .profile-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .profile-detail-list {
            display: grid;
            gap: 0.5rem;
        }

        @media (max-width: 860px) {
            .profile-page {
                grid-template-columns: 1fr;
            }

            .profile-summary {
                position: static;
            }
        }

        @media (max-width: 560px) {
            .profile-form-grid {
                grid-template-columns: 1fr;
            }

            .profile-section-header,
            .profile-actions {
                align-items: stretch;
                flex-direction: column;
            }

            .profile-actions .ghost-button,
            .profile-actions .primary-button {
                width: 100%;
            }
        }
    </style>

    <div class="profile-page">
        <aside class="profile-summary profile-section">
            <div class="profile-identity">
                <div class="profile-avatar">{{ $initials ?: 'U' }}</div>
                <div>
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $user->email }}</p>
                </div>
            </div>

            <div class="profile-detail-list">
                <div class="detail-item">
                    <span class="detail-label">Role</span>
                    <div class="detail-value">{{ $roleNames ?: 'None assigned' }}</div>
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
                    <span class="detail-label">Account status</span>
                    <div class="detail-value">{{ $user->locked_until && $user->locked_until->isFuture() ? 'Temporarily locked' : 'Active' }}</div>
                </div>
            </div>

            <p class="profile-note">Role and branch access are controlled by administration so audit trails and clinic permissions stay consistent.</p>
        </aside>

        <div class="profile-stack">
            <form method="POST" action="{{ route('profile.update') }}" class="profile-stack">
                @csrf
                @method('PUT')

                <section class="profile-section">
                    <div class="profile-section-header">
                        <div>
                            <h3>Personal details</h3>
                            <p>These details are used for login, audit trails, messages, and staff identification.</p>
                        </div>
                    </div>

                    <div class="profile-form-grid">
                        <div class="field">
                            <label for="name">Full name</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                            @error('name')<p class="subtle">{{ $message }}</p>@enderror
                        </div>

                        <div class="field">
                            <label for="email">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                            @error('email')<p class="subtle">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="profile-section">
                    <div class="profile-section-header">
                        <div>
                            <h3>Change password</h3>
                            <p>Enter your current password only when setting a new password.</p>
                        </div>
                    </div>

                    <div class="profile-form-grid">
                        <div class="field field-wide">
                            <label for="current_password">Current password</label>
                            <input id="current_password" type="password" name="current_password" autocomplete="current-password">
                            @error('current_password')<p class="subtle">{{ $message }}</p>@enderror
                        </div>

                        <div class="field">
                            <label for="password">New password</label>
                            <input id="password" type="password" name="password" autocomplete="new-password">
                            @error('password')<p class="subtle">{{ $message }}</p>@enderror
                        </div>

                        <div class="field">
                            <label for="password_confirmation">Confirm new password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>
                </section>

                <section class="profile-section">
                    <div class="profile-section-header">
                        <div>
                            <h3>System access</h3>
                            <p>Contact an administrator if your role, branch, or module access needs to change.</p>
                        </div>
                    </div>

                    <div class="profile-form-grid">
                        <div class="detail-item">
                            <span class="detail-label">Role</span>
                            <div class="detail-value">{{ $roleNames ?: 'None assigned' }}</div>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Branch</span>
                            <div class="detail-value">{{ optional($user->branch)->name ?? 'Global access' }}</div>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Failed login count</span>
                            <div class="detail-value">{{ $user->failed_login_count ?? 0 }}</div>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Member since</span>
                            <div class="detail-value">{{ $user->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>
                </section>

                <div class="profile-actions">
                    <a class="ghost-button" href="{{ $user->isSuperAdmin() ? route('admin.dashboard') : route('staff.dashboard') }}">Cancel</a>
                    <button class="primary-button" type="submit">Save profile</button>
                </div>
            </form>
        </div>
    </div>
@endsection

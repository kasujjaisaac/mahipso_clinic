@extends('layouts.app')

@section('title', 'Clinic Dashboard')
@section('section', 'Clinic Overview')
@section('kicker', 'Operations Command Center')
@section('page_title', 'Clinic dashboard')
@section('page_subtitle', 'Choose a clinic to open its operational dashboard, review workloads, and move into patients, appointments, visits, and records.')

@section('topbar_actions')
    @if(optional(auth()->user())->hasRole('super_admin'))
        <a class="ghost-button" href="{{ route('branches.index') }}">Manage branches</a>
        <a class="ghost-button" href="{{ route('admin.dashboard') }}">Admin dashboard</a>
    @endif
@endsection

@section('content')
    <div class="stats-grid">
        <div class="metric-card" style="--accent: var(--brand);">
            <div class="metric-icon">⌂</div>
            <div>
                <div class="metric-value">{{ $branches->count() }}</div>
                <div class="metric-label">Active clinics</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: var(--green);">
            <div class="metric-icon">◎</div>
            <div>
                <div class="metric-value">{{ $branches->where('status', 'active')->count() }}</div>
                <div class="metric-label">Available branches</div>
            </div>
        </div>
        <div class="metric-card" style="--accent: var(--blue);">
            <div class="metric-icon">⇄</div>
            <div>
                <div class="metric-value">6</div>
                <div class="metric-label">Core workflows</div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Clinic quick access</h2>
                <p class="subtle">Every clinic card opens a focused dashboard with shortcuts to the same modules shown in the sidebar.</p>
            </div>
        </div>

        <div class="card-grid">
            @foreach($branches as $branch)
                <a class="entity-card" href="{{ route('branches.dashboard', $branch) }}">
                    <h3>{{ $branch->name }}</h3>
                    <p>{{ $branch->address ?: 'Address not yet recorded.' }}</p>
                    <div class="inline-actions" style="margin-top: 1rem;">
                        <span class="status-pill {{ $branch->status }}">{{ ucfirst($branch->status) }}</span>
                        <span class="chip">Open dashboard</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection

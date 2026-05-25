@extends('layouts.app')

@section('title', 'Staff Portal')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('staff.dashboard') }}">Staff Portal</a>
@endsection

@section('content')
<style>
    .portal-header {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .portal-title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 700;
    }
    .portal-role {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.55rem;
        border: 1px solid var(--line);
        background: #fff8f7;
        color: var(--brand);
        font-weight: 700;
    }
    .portal-table td {
        vertical-align: middle;
    }
    .portal-actions {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
    }
    .quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.5rem;
    }
    .quick-link {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid var(--line);
        background: #ffffff;
        padding: 0.62rem;
        font-weight: 700;
    }
    .quick-link:hover {
        color: var(--brand);
        background: #fff8f7;
    }
</style>

<div class="panel">
    <div class="portal-header">
        <div>
            <h2 class="portal-title">Staff Portal</h2>
            <p class="subtle" style="margin: 0.2rem 0 0;">{{ auth()->user()->name }} · {{ optional(auth()->user()->branch)->name ?? 'Clinic branch' }}</p>
        </div>
        <span class="portal-role">{{ $role }}</span>
    </div>
</div>

<div class="stats-grid">
    @foreach($cards as $card)
        <div class="metric-card" style="--accent: {{ $card['accent'] }};">
            <div class="metric-icon">{{ substr($card['label'], 0, 1) }}</div>
            <div>
                <p class="metric-value">{{ number_format($card['value']) }}</p>
                <p class="metric-label">{{ $card['label'] }}</p>
            </div>
        </div>
    @endforeach
</div>

<div class="panel">
    <div class="panel-header">
        <h2 class="section-title">My Work Queue</h2>
        <span class="chip">{{ $workQueue->count() }} waiting</span>
    </div>
    <div class="table-wrap">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>Task</th>
                    <th>Patient</th>
                    <th>Status</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($workQueue as $item)
                    <tr>
                        <td>{{ $item['type'] }}</td>
                        <td>{{ $item['patient'] }}</td>
                        <td><span class="status-pill active">{{ $item['status'] }}</span></td>
                        <td>{{ $item['time'] ?: 'N/A' }}</td>
                        <td><a class="ghost-button" href="{{ $item['url'] }}">{{ $item['action'] }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No work is waiting right now.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if(auth()->user()->hasRole('doctor'))
    <div class="panel">
        <div class="panel-header">
            <h2 class="section-title">Doctor Inpatient Tasks</h2>
            <a class="ghost-button" href="{{ route('admissions.index') }}">All admissions</a>
        </div>
        <div class="table-wrap">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Ward / Bed</th>
                        <th>Diagnosis</th>
                        <th>Status</th>
                        <th>Tasks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctorInpatients as $admission)
                        <tr>
                            <td>{{ $admission->patient->full_name ?? 'N/A' }}<br><span class="subtle">{{ $admission->admission_no }}</span></td>
                            <td>{{ $admission->ward->name ?? 'No ward' }} / {{ $admission->bed->bed_number ?? 'No bed' }}</td>
                            <td>{{ $admission->current_diagnosis ?: $admission->provisional_diagnosis ?: 'Pending' }}</td>
                            <td><span class="status-pill active">{{ $admission->status_label }}</span></td>
                            <td>
                                <div class="portal-actions">
                                    <a class="ghost-button" href="{{ route('admissions.show', $admission) }}">Progress notes</a>
                                    <a class="ghost-button" href="{{ route('admissions.show', $admission) }}#medications">Medication orders</a>
                                    <a class="ghost-button" href="{{ route('admissions.show', $admission) }}#discharge">Discharge</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-state">No inpatient doctor tasks.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <h2 class="section-title">Recent Lab Results</h2>
            <a class="ghost-button" href="{{ route('laboratory.index') }}">Laboratory</a>
        </div>
        @include('dashboards.partials.lab_tests', ['labTests' => $recentLabResults])
    </div>
@endif

@if(auth()->user()->hasRole('nurse'))
    <div class="panel">
        <div class="panel-header">
            <h2 class="section-title">Nursing Inpatient Tasks</h2>
            <a class="ghost-button" href="{{ route('admissions.index') }}">Ward patients</a>
        </div>
        <div class="table-wrap">
            <table class="portal-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Ward / Bed</th>
                        <th>Last Vitals</th>
                        <th>Status</th>
                        <th>Tasks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nurseInpatients as $admission)
                        @php
                            $latestVital = $admission->vitals->sortByDesc('recorded_at')->first();
                        @endphp
                        <tr>
                            <td>{{ $admission->patient->full_name ?? 'N/A' }}<br><span class="subtle">{{ $admission->admission_no }}</span></td>
                            <td>{{ $admission->ward->name ?? 'No ward' }} / {{ $admission->bed->bed_number ?? 'No bed' }}</td>
                            <td>{{ $latestVital?->recorded_at?->format('M d, H:i') ?? 'Not recorded' }}</td>
                            <td><span class="status-pill active">{{ $admission->status_label }}</span></td>
                            <td>
                                <div class="portal-actions">
                                    <a class="ghost-button" href="{{ route('admissions.show', $admission) }}#vitals">Record vitals</a>
                                    <a class="ghost-button" href="{{ route('admissions.show', $admission) }}#medications">Administer medicine</a>
                                    <a class="ghost-button" href="{{ route('admissions.show', $admission) }}#notes">Nursing note</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-state">No nursing inpatient tasks.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

@if(auth()->user()->hasRole(['pharmacist', 'branch_admin']) && $lowStock->isNotEmpty())
    <div class="panel">
        <div class="panel-header">
            <h2 class="section-title">Stock Alerts</h2>
            <a class="ghost-button" href="{{ route('inventory.index') }}">Inventory</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Item</th><th>Quantity</th><th>Reorder Level</th><th>Location</th></tr></thead>
                <tbody>
                    @foreach($lowStock as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->quantity }} {{ $item->unit }}</td>
                            <td>{{ $item->reorder_level }} {{ $item->unit }}</td>
                            <td>{{ $item->location ?: 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="panel">
    <div class="panel-header">
        <h2 class="section-title">Quick Actions</h2>
    </div>
    <div class="quick-links">
        @if(auth()->user()->hasRole(['doctor', 'nurse', 'receptionist', 'branch_admin']))
            <a class="quick-link" href="{{ route('patients.index') }}"><span>Patients</span><span>Open</span></a>
            <a class="quick-link" href="{{ route('clinic-queue.index') }}"><span>Clinic Queue</span><span>Open</span></a>
        @endif
        @if(auth()->user()->hasRole(['doctor', 'nurse', 'branch_admin']))
            <a class="quick-link" href="{{ route('admissions.index') }}"><span>Admissions</span><span>Open</span></a>
        @endif
        @if(auth()->user()->hasRole(['doctor', 'labtech', 'branch_admin']))
            <a class="quick-link" href="{{ route('laboratory.index') }}"><span>Laboratory</span><span>Open</span></a>
        @endif
        @if(auth()->user()->hasRole(['doctor', 'nurse', 'pharmacist', 'branch_admin']))
            <a class="quick-link" href="{{ route('prescriptions.index') }}"><span>Prescriptions</span><span>Open</span></a>
        @endif
        @if(auth()->user()->hasRole(['receptionist', 'finance_officer', 'branch_admin']))
            <a class="quick-link" href="{{ route('billing.index') }}"><span>Billing</span><span>Open</span></a>
        @endif
        @if(auth()->user()->hasRole(['hr_manager', 'branch_admin']))
            <a class="quick-link" href="{{ route('employees.index') }}"><span>Staff Registry</span><span>Open</span></a>
            <a class="quick-link" href="{{ route('attendance.index') }}"><span>Attendance</span><span>Open</span></a>
        @endif
        <a class="quick-link" href="{{ route('requisitions.mine') }}"><span>My Requisitions</span><span>Open</span></a>
        <a class="quick-link" href="{{ route('timesheets.mine') }}"><span>My Timesheets</span><span>Open</span></a>
    </div>
</div>
@endsection

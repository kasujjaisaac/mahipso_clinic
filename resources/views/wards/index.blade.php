@extends('layouts.app')

@section('title', 'Ward Bed Board')
@section('section', 'Inpatient Management')
@section('kicker', 'Bed Board')
@section('page_title', 'Wards and beds')
@section('page_subtitle', 'Track bed availability, ward occupancy, and current admitted patients.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('wards.create') }}">New ward</a>
    <a class="ghost-button" href="{{ route('admissions.index') }}">Admissions</a>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="metric-card"><div class="metric-icon">{{ $wards->count() }}</div><div><div class="metric-value">{{ $wards->count() }}</div><div class="metric-label">Wards</div></div></div>
        <div class="metric-card"><div class="metric-icon">{{ $availableBeds }}</div><div><div class="metric-value">{{ $availableBeds }}</div><div class="metric-label">Available beds</div></div></div>
        <div class="metric-card"><div class="metric-icon">{{ $activeAdmissions }}</div><div><div class="metric-value">{{ $activeAdmissions }}</div><div class="metric-label">Occupied beds</div></div></div>
    </div>

    @foreach($wards as $ward)
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="section-title"><a href="{{ route('wards.show', $ward) }}">{{ $ward->name }}</a></h2>
                    <p class="table-meta">{{ optional($ward->branch)->name }} - {{ ucfirst($ward->type) }} - {{ $ward->beds->count() }} beds</p>
                </div>
            </div>

            <div class="card-grid">
                @forelse($ward->beds as $bed)
                    <div class="info-card">
                        <h3>Bed {{ $bed->bed_number }}</h3>
                        <p><span class="status-pill {{ $bed->status }}">{{ ucfirst($bed->status) }}</span></p>
                        @if($bed->currentAdmission)
                            <p><strong>{{ $bed->currentAdmission->patient->full_name }}</strong></p>
                            <p class="subtle">{{ $bed->currentAdmission->admission_no }}</p>
                            <a class="ghost-button" href="{{ route('admissions.show', $bed->currentAdmission) }}">Open chart</a>
                        @else
                            <p class="subtle">{{ $bed->notes ?: 'No patient assigned.' }}</p>
                        @endif
                    </div>
                @empty
                    <div class="empty-state">No beds configured for this ward.</div>
                @endforelse
            </div>
        </div>
    @endforeach
@endsection

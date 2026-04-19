@extends('layouts.app')

@section('title', 'Visit Details')
@section('section', 'Clinical Operations')
@section('kicker', 'Visit Desk')
@section('page_title', 'Visit #' . $visit->id)
@section('page_subtitle', 'Encounter summary with patient context, provider details, and related medical records.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('vitals.create', $visit) }}">Record Vital Signs</a>
    @if(isset($pharmacyForVisit))
        <a class="ghost-button" href="{{ route('pharmacies.sales.create', [$pharmacyForVisit, 'patient_id' => $visit->patient_id, 'visit_id' => $visit->id]) }}">Send prescription to pharmacy</a>
    @endif
    <a class="ghost-button" href="{{ route('visits.index') }}">Back</a>
    <a class="ghost-button" href="{{ route('medical-records.create') }}">New medical record</a>
    <a class="ghost-button" href="{{ route('hiv-records.create') }}?visit_id={{ $visit->id }}">New HIV record</a>
@endsection

@section('content')
    <div class="panel">
        <div class="detail-grid">
            <div class="detail-item"><span class="detail-label">Patient</span><div class="detail-value">{{ $visit->patient->full_name ?? 'N/A' }}</div></div>
            <div class="detail-item"><span class="detail-label">Visit date</span><div class="detail-value">{{ $visit->visit_date->format('Y-m-d H:i') }}</div></div>
            <div class="detail-item"><span class="detail-label">Visit type</span><div class="detail-value">{{ ucfirst($visit->visit_type) }}</div></div>
            <div class="detail-item"><span class="detail-label">Provider</span><div class="detail-value">{{ $visit->provider->name ?? 'N/A' }}</div></div>
            <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value"><span class="status-pill {{ $visit->status }}">{{ ucfirst($visit->status) }}</span></div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Chief complaint</span><div class="detail-value">{{ $visit->chief_complaint ?: 'No complaint recorded.' }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Notes</span><div class="detail-value">{{ $visit->notes ?: 'No notes recorded.' }}</div></div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Linked medical records</h2>
                <p class="subtle">Records created during this visit appear here.</p>
            </div>
        </div>

        @if($visit->medicalRecords->isEmpty())
            <div class="empty-state">No medical records have been attached to this visit yet.</div>
        @else
            <div class="card-grid">
                @foreach($visit->medicalRecords as $record)
                    <a class="info-card" href="{{ route('medical-records.show', $record) }}">
                        <h3>Record #{{ $record->id }}</h3>
                        <p>{{ $record->created_at->format('Y-m-d H:i') }}</p>
                        <p style="margin-top: 0.55rem;">{{ \Illuminate\Support\Str::limit($record->diagnosis, 120) ?: 'No diagnosis entered yet.' }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Visit Details')
@section('section', 'Clinical Operations')
@section('kicker', 'Visit Desk')
@section('page_title', 'Visit #' . $visit->id)
@section('page_subtitle', 'Encounter summary with patient context, provider details, and related medical records.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('clinic-queue.index') }}">Clinic queue</a>
    <a class="primary-button" href="{{ route('vitals.create', $visit) }}">Record Vital Signs</a>
    @if($visit->admission)
        <a class="ghost-button" href="{{ route('admissions.show', $visit->admission) }}">Inpatient chart</a>
    @else
        <a class="ghost-button" href="{{ route('admissions.create', ['visit_id' => $visit->id]) }}">Admit patient</a>
    @endif
    @if(isset($pharmacyForVisit))
        <a class="ghost-button" href="{{ route('pharmacies.sales.create', [$pharmacyForVisit, 'patient_id' => $visit->patient_id, 'visit_id' => $visit->id]) }}">Send prescription to pharmacy</a>
    @endif
    <a class="ghost-button" href="{{ route('visits.index') }}">Back</a>
    <a class="ghost-button" href="{{ route('medical-records.create') }}">New medical record</a>
    <a class="ghost-button" href="{{ route('hiv-records.create') }}?visit_id={{ $visit->id }}">New HIV record</a>
    <a class="ghost-button" href="{{ route('laboratory.create', ['visit_id' => $visit->id]) }}">Order lab</a>
    <a class="ghost-button" href="{{ route('prescriptions.create', ['visit_id' => $visit->id]) }}">Prescribe</a>
@endsection

@section('content')
    <div class="panel">
        <div class="detail-grid">
            <div class="detail-item"><span class="detail-label">Patient</span><div class="detail-value">{{ $visit->patient->full_name ?? 'N/A' }}</div></div>
            <div class="detail-item"><span class="detail-label">Visit date</span><div class="detail-value">{{ $visit->visit_date->format('Y-m-d H:i') }}</div></div>
            <div class="detail-item"><span class="detail-label">Visit type</span><div class="detail-value">{{ ucfirst($visit->visit_type) }}</div></div>
            <div class="detail-item"><span class="detail-label">Provider</span><div class="detail-value">{{ $visit->provider->name ?? 'N/A' }}</div></div>
            <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value"><span class="status-pill {{ $visit->status }}">{{ ucfirst($visit->status) }}</span></div></div>
            <div class="detail-item"><span class="detail-label">Workflow stage</span><div class="detail-value"><span class="status-pill {{ $visit->workflow_stage }}">{{ $visit->workflow_stage_label }}</span></div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Chief complaint</span><div class="detail-value">{{ $visit->chief_complaint ?: 'No complaint recorded.' }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Notes</span><div class="detail-value">{{ $visit->notes ?: 'No notes recorded.' }}</div></div>
        </div>
    </div>

    @if($visit->admission)
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="section-title">Inpatient admission</h2>
                    <p class="subtle">This visit has been admitted to ward care.</p>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-item"><span class="detail-label">Admission</span><div class="detail-value"><a href="{{ route('admissions.show', $visit->admission) }}">{{ $visit->admission->admission_no }}</a></div></div>
                <div class="detail-item"><span class="detail-label">Ward / Bed</span><div class="detail-value">{{ $visit->admission->ward->name ?? 'Ward' }} / {{ $visit->admission->bed->bed_number ?? 'Bed' }}</div></div>
                <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value"><span class="status-pill {{ $visit->admission->status }}">{{ $visit->admission->status_label }}</span></div></div>
            </div>
        </div>
    @endif

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Generate bill</h2>
                <p class="subtle">Create a bill from consultation fee, lab orders, and prescription items.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('visits.billing.generate', $visit) }}" class="toolbar-form">
            @csrf
            <input type="number" name="consultation_fee" min="0" step="0.01" placeholder="Consultation fee">
            <button class="primary-button" type="submit">Generate bill</button>
        </form>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Move patient</h2>
                <p class="subtle">Update where this patient is in today’s clinic flow.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('visits.workflow.update', $visit) }}" class="toolbar-form">
            @csrf
            @method('PATCH')
            <select name="workflow_stage">
                @foreach(\App\Models\Visit::WORKFLOW_STAGES as $stage => $label)
                    <option value="{{ $stage }}" {{ $stage === $visit->workflow_stage ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="primary-button" type="submit">Update stage</button>
        </form>
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

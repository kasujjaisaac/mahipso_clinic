@extends('layouts.app')

@section('title', 'HIV Record Details')
@section('section', 'Clinical Records')
@section('kicker', 'HIV Monitoring')
@section('page_title', 'HIV record #' . $hivRecord->id)
@section('page_subtitle', 'Detailed HIV test and treatment-support information linked to a visit and patient.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('hiv-records.index') }}">Back</a>
    <a class="primary-button" href="{{ route('hiv-records.edit', $hivRecord) }}">Edit record</a>
@endsection

@section('content')
    <div class="panel">
        <div class="detail-grid">
            <div class="detail-item"><span class="detail-label">Visit</span><div class="detail-value">#{{ $hivRecord->visit->id ?? 'N/A' }}</div></div>
            <div class="detail-item"><span class="detail-label">Patient</span><div class="detail-value">{{ $hivRecord->patient->full_name ?? 'N/A' }}</div></div>
            <div class="detail-item"><span class="detail-label">Provider</span><div class="detail-value">{{ $hivRecord->provider->name ?? 'N/A' }}</div></div>
            <div class="detail-item"><span class="detail-label">Test type</span><div class="detail-value">{{ ucfirst($hivRecord->test_type) }}</div></div>
            <div class="detail-item"><span class="detail-label">Result</span><div class="detail-value"><span class="status-pill {{ $hivRecord->test_result }}">{{ ucfirst($hivRecord->test_result) }}</span></div></div>
            <div class="detail-item"><span class="detail-label">CD4 count</span><div class="detail-value">{{ $hivRecord->cd4_count ?? 'n/a' }}</div></div>
            <div class="detail-item"><span class="detail-label">Viral load</span><div class="detail-value">{{ $hivRecord->viral_load ?? 'n/a' }}</div></div>
            <div class="detail-item"><span class="detail-label">ART status</span><div class="detail-value">{{ $hivRecord->art_status ?: 'Not recorded' }}</div></div>
            <div class="detail-item"><span class="detail-label">Regimen</span><div class="detail-value">{{ $hivRecord->regimen ?: 'Not recorded' }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Adherence</span><div class="detail-value">{{ $hivRecord->adherence ?: 'No adherence notes recorded.' }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Notes</span><div class="detail-value">{{ $hivRecord->notes ?: 'No notes recorded.' }}</div></div>
            <div class="detail-item"><span class="detail-label">Created</span><div class="detail-value">{{ $hivRecord->created_at->format('Y-m-d H:i') }}</div></div>
            <div class="detail-item"><span class="detail-label">Updated</span><div class="detail-value">{{ $hivRecord->updated_at->format('Y-m-d H:i') }}</div></div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Medical Record Details')
@section('section', 'Clinical Records')
@section('kicker', 'Medical Notes')
@section('page_title', 'Medical record #' . $medicalRecord->id)
@section('page_subtitle', 'Detailed clinical documentation linked to a visit, provider, and patient.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('medical-records.index') }}">Back</a>
    <a class="primary-button" href="{{ route('medical-records.edit', $medicalRecord) }}">Edit record</a>
@endsection

@section('content')
    <div class="panel">
        <div class="detail-grid">
            <div class="detail-item"><span class="detail-label">Visit</span><div class="detail-value">#{{ $medicalRecord->visit->id ?? 'N/A' }}</div></div>
            <div class="detail-item"><span class="detail-label">Patient</span><div class="detail-value">{{ $medicalRecord->patient->full_name ?? ($medicalRecord->visit->patient->full_name ?? 'N/A') }}</div></div>
            <div class="detail-item"><span class="detail-label">Provider</span><div class="detail-value">{{ $medicalRecord->provider->name ?? 'N/A' }}</div></div>
            <div class="detail-item"><span class="detail-label">Created at</span><div class="detail-value">{{ $medicalRecord->created_at->format('Y-m-d H:i') }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Symptoms</span><div class="detail-value">{{ $medicalRecord->symptoms ?: 'No symptoms recorded.' }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Diagnosis</span><div class="detail-value">{{ $medicalRecord->diagnosis ?: 'No diagnosis recorded.' }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Treatment</span><div class="detail-value">{{ $medicalRecord->treatment ?: 'No treatment recorded.' }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Plan</span><div class="detail-value">{{ $medicalRecord->plan ?: 'No plan recorded.' }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Notes</span><div class="detail-value">{{ $medicalRecord->notes ?: 'No notes recorded.' }}</div></div>
        </div>
    </div>
@endsection

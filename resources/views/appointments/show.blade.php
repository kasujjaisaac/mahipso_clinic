@extends('layouts.app')

@section('title', 'Appointment Details')
@section('section', 'Scheduling')
@section('kicker', 'Appointment Desk')
@section('page_title', 'Appointment #' . $appointment->id)
@section('page_subtitle', 'Detailed view of branch assignment, patient, doctor, schedule, and appointment notes.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('appointments.index') }}">Back</a>
    <a class="primary-button" href="{{ route('appointments.edit', $appointment) }}">Edit appointment</a>
@endsection

@section('content')
    <div class="panel">
        <div class="detail-grid">
            <div class="detail-item"><span class="detail-label">Branch</span><div class="detail-value">{{ optional($appointment->branch)->name ?: 'Unassigned' }}</div></div>
            <div class="detail-item"><span class="detail-label">Patient</span><div class="detail-value">{{ optional($appointment->patient)->full_name ?: 'N/A' }}</div></div>
            <div class="detail-item"><span class="detail-label">Doctor</span><div class="detail-value">{{ optional($appointment->doctor)->name ?: 'Unassigned' }}</div></div>
            <div class="detail-item"><span class="detail-label">Service type</span><div class="detail-value">{{ $appointment->service_type ?: 'Not set' }}</div></div>
            <div class="detail-item"><span class="detail-label">Scheduled at</span><div class="detail-value">{{ $appointment->scheduled_at->format('Y-m-d H:i') }}</div></div>
            <div class="detail-item"><span class="detail-label">Duration</span><div class="detail-value">{{ $appointment->duration }} minutes</div></div>
            <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value"><span class="status-pill {{ $appointment->status }}">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</span></div></div>
            <div class="detail-item"><span class="detail-label">Created</span><div class="detail-value">{{ $appointment->created_at }}</div></div>
            <div class="detail-item"><span class="detail-label">Updated</span><div class="detail-value">{{ $appointment->updated_at }}</div></div>
            <div class="detail-item field-span-2"><span class="detail-label">Notes</span><div class="detail-value">{{ $appointment->notes ?: 'No notes recorded.' }}</div></div>
        </div>
    </div>
@endsection

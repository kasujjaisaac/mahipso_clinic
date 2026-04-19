@extends('layouts.app')

@section('title', 'Create Visit')
@section('section', 'Clinical Operations')
@section('kicker', 'Visit Desk')
@section('page_title', 'Create visit')
@section('page_subtitle', 'Open a patient visit, attach an appointment when needed, and record the initial clinical context.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('visits.index') }}">Back to visits</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('visits.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label for="patient_id">Patient</label>
                    <select id="patient_id" name="patient_id" required>
                        <option value="">Choose patient</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id', request('patient_id')) == $p->id ? 'selected' : '' }}>{{ $p->mrn }} - {{ $p->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="appointment_id">Appointment</label>
                    <select id="appointment_id" name="appointment_id">
                        <option value="">None</option>
                        @foreach($appointments as $a)
                            <option value="{{ $a->id }}" {{ old('appointment_id') == $a->id ? 'selected' : '' }}>#{{ $a->id }} ({{ $a->scheduled_at->format('Y-m-d H:i') }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="provider_id">Provider</label>
                    <select id="provider_id" name="provider_id">
                        <option value="">Optional provider</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="visit_date">Visit date</label>
                    <input id="visit_date" type="datetime-local" name="visit_date" value="{{ old('visit_date') ?? now()->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="field">
                    <label for="visit_type">Visit type</label>
                    <select id="visit_type" name="visit_type" required>
                        @foreach(['general','hiv','counseling','lab','pharmacy','other'] as $type)
                            <option value="{{ $type }}" {{ old('visit_type', 'general') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        @foreach(['open','closed','cancelled'] as $status)
                            <option value="{{ $status }}" {{ old('status', 'open') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field field-span-2">
                    <label for="chief_complaint">Chief complaint</label>
                    <textarea id="chief_complaint" name="chief_complaint">{{ old('chief_complaint') }}</textarea>
                </div>
                <div class="field field-span-2">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="action-stack" style="margin-top: 1.25rem;">
                <button class="primary-button" type="submit">Save visit</button>
                <a class="ghost-button" href="{{ route('visits.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Visit')
@section('section', 'Clinical Operations')
@section('kicker', 'Visit Desk')
@section('page_title', 'Edit visit #' . $visit->id)
@section('page_subtitle', 'Update visit details, provider, status, and encounter notes.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('visits.index') }}">Back to visits</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('visits.update', $visit) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label for="patient_id">Patient</label>
                    <select id="patient_id" name="patient_id" required>
                        <option value="">Choose patient</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id', $visit->patient_id) == $p->id ? 'selected' : '' }}>{{ $p->mrn }} - {{ $p->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="provider_id">Provider</label>
                    <select id="provider_id" name="provider_id">
                        <option value="">Optional provider</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}" {{ old('provider_id', $visit->provider_id) == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="visit_date">Visit date</label>
                    <input id="visit_date" type="datetime-local" name="visit_date" value="{{ old('visit_date', $visit->visit_date->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="field">
                    <label for="visit_type">Visit type</label>
                    <select id="visit_type" name="visit_type" required>
                        @foreach(['general','hiv','counseling','lab','pharmacy','other'] as $type)
                            <option value="{{ $type }}" {{ old('visit_type', $visit->visit_type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        @foreach(['open','closed','cancelled'] as $status)
                            <option value="{{ $status }}" {{ old('status', $visit->status) == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field field-span-2">
                    <label for="chief_complaint">Chief complaint</label>
                    <textarea id="chief_complaint" name="chief_complaint">{{ old('chief_complaint', $visit->chief_complaint) }}</textarea>
                </div>
                <div class="field field-span-2">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes">{{ old('notes', $visit->notes) }}</textarea>
                </div>
            </div>
            <div class="action-stack" style="margin-top: 1.25rem;">
                <button class="primary-button" type="submit">Update visit</button>
                <a class="ghost-button" href="{{ route('visits.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection

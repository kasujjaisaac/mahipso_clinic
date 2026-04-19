@extends('layouts.app')

@section('title', 'Create Medical Record')
@section('section', 'Clinical Records')
@section('kicker', 'Medical Notes')
@section('page_title', 'Create medical record')
@section('page_subtitle', 'Capture symptoms, diagnosis, treatment, and plan for a specific patient visit.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('medical-records.index') }}">Back to records</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('medical-records.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label for="visit_id">Visit</label>
                    <select id="visit_id" name="visit_id" required>
                        <option value="">Choose visit</option>
                        @foreach($visits as $visit)
                            <option value="{{ $visit->id }}" {{ old('visit_id', request('visit_id')) == $visit->id ? 'selected' : '' }}>#{{ $visit->id }} - {{ $visit->patient->full_name }} ({{ $visit->visit_type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="patient_id">Patient</label>
                    <select id="patient_id" name="patient_id">
                        <option value="">Choose patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id', request('patient_id')) == $patient->id ? 'selected' : '' }}>{{ $patient->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="provider_id">Provider</label>
                    <select id="provider_id" name="provider_id">
                        <option value="">Choose provider</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->id }}" {{ old('provider_id') == $provider->id ? 'selected' : '' }}>{{ $provider->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field field-span-2">
                    <label for="symptoms">Symptoms</label>
                    <textarea id="symptoms" name="symptoms">{{ old('symptoms') }}</textarea>
                </div>
                <div class="field field-span-2">
                    <label for="diagnosis">Diagnosis</label>
                    <textarea id="diagnosis" name="diagnosis">{{ old('diagnosis') }}</textarea>
                </div>
                <div class="field field-span-2">
                    <label for="treatment">Treatment</label>
                    <textarea id="treatment" name="treatment">{{ old('treatment') }}</textarea>
                </div>
                <div class="field field-span-2">
                    <label for="plan">Plan</label>
                    <textarea id="plan" name="plan">{{ old('plan') }}</textarea>
                </div>
                <div class="field field-span-2">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="action-stack" style="margin-top: 1.25rem;">
                <button class="primary-button" type="submit">Create record</button>
                <a class="ghost-button" href="{{ route('medical-records.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection

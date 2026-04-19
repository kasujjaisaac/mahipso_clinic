@extends('layouts.app')

@section('title', 'Record Vital Signs')
@section('section', 'Clinical Operations')
@section('kicker', 'Triage')
@section('page_title', 'Record Vital Signs')
@section('page_subtitle', 'Enter vital signs for this visit.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('visits.show', $visit) }}">Back to visit</a>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('vitals.store', $visit) }}">
        @csrf
        <div class="form-grid">
            <div class="field">
                <label for="weight">Weight (kg)</label>
                <input id="weight" name="weight" type="number" step="0.1" value="{{ old('weight') }}">
            </div>
            <div class="field">
                <label for="height">Height (cm)</label>
                <input id="height" name="height" type="number" step="0.1" value="{{ old('height') }}">
            </div>
            <div class="field">
                <label for="temperature">Temperature (°C)</label>
                <input id="temperature" name="temperature" type="number" step="0.1" value="{{ old('temperature') }}">
            </div>
            <div class="field">
                <label for="blood_pressure_systolic">BP Systolic (mmHg)</label>
                <input id="blood_pressure_systolic" name="blood_pressure_systolic" type="number" step="1" value="{{ old('blood_pressure_systolic') }}">
            </div>
            <div class="field">
                <label for="blood_pressure_diastolic">BP Diastolic (mmHg)</label>
                <input id="blood_pressure_diastolic" name="blood_pressure_diastolic" type="number" step="1" value="{{ old('blood_pressure_diastolic') }}">
            </div>
            <div class="field">
                <label for="pulse">Pulse (bpm)</label>
                <input id="pulse" name="pulse" type="number" step="1" value="{{ old('pulse') }}">
            </div>
            <div class="field">
                <label for="respiratory_rate">Respiratory Rate (bpm)</label>
                <input id="respiratory_rate" name="respiratory_rate" type="number" step="1" value="{{ old('respiratory_rate') }}">
            </div>
            <div class="field">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
            </div>
        </div>
        <button class="primary-button" type="submit">Save Vital Signs</button>
    </form>
</div>
@endsection

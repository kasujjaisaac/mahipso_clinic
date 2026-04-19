@extends('layouts.app')

@section('title', 'Create HIV Record')
@section('section', 'Clinical Records')
@section('kicker', 'HIV Monitoring')
@section('page_title', 'Create HIV record')
@section('page_subtitle', 'Capture HIV test results, viral load, regimen details, and adherence notes for a visit.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('hiv-records.index') }}">Back to HIV records</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('hiv-records.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label for="visit_id">Visit</label>
                    <select id="visit_id" name="visit_id" required>
                        <option value="">Choose visit</option>
                        @foreach($visits as $visit)
                            <option value="{{ $visit->id }}" {{ (old('visit_id') == $visit->id || (!old('visit_id') && isset($visitId) && $visitId == $visit->id)) ? 'selected' : '' }}>#{{ $visit->id }} - {{ $visit->patient->full_name }} ({{ $visit->visit_type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="patient_id">Patient</label>
                    <select id="patient_id" name="patient_id">
                        <option value="">Use visit patient</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>{{ $patient->full_name }}</option>
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
                <div class="field">
                    <label for="test_type">Test type</label>
                    <select id="test_type" name="test_type" required>
                        @foreach(['rapid','elisa','pcr','viral_load','cd4','other'] as $t)
                            <option value="{{ $t }}" {{ old('test_type', 'rapid') == $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="test_result">Test result</label>
                    <select id="test_result" name="test_result" required>
                        @foreach(['negative','positive','indeterminate','unknown'] as $r)
                            <option value="{{ $r }}" {{ old('test_result', 'unknown') == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="cd4_count">CD4 count</label>
                    <input id="cd4_count" type="number" name="cd4_count" value="{{ old('cd4_count') }}">
                </div>
                <div class="field">
                    <label for="viral_load">Viral load</label>
                    <input id="viral_load" type="number" name="viral_load" value="{{ old('viral_load') }}">
                </div>
                <div class="field">
                    <label for="art_status">ART status</label>
                    <input id="art_status" name="art_status" value="{{ old('art_status') }}">
                </div>
                <div class="field">
                    <label for="regimen">Regimen</label>
                    <input id="regimen" name="regimen" value="{{ old('regimen') }}">
                </div>
                <div class="field field-span-2">
                    <label for="adherence">Adherence</label>
                    <textarea id="adherence" name="adherence">{{ old('adherence') }}</textarea>
                </div>
                <div class="field field-span-2">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="action-stack" style="margin-top: 1.25rem;">
                <button class="primary-button" type="submit">Save HIV record</button>
                <a class="ghost-button" href="{{ route('hiv-records.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Admit Patient')
@section('section', 'Inpatient Management')
@section('kicker', 'Admission')
@section('page_title', 'Admit patient')
@section('page_subtitle', 'Assign ward, bed, admitting clinician, diagnosis, and initial care plan.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('admissions.index') }}">Admissions</a>
    <a class="ghost-button" href="{{ route('wards.index') }}">Bed board</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('admissions.store') }}" class="form-grid">
            @csrf
            @if($visit)<input type="hidden" name="visit_id" value="{{ $visit->id }}">@endif

            <div>
                <label>Patient</label>
                <select name="patient_id" required>
                    @foreach($patients as $option)
                        <option value="{{ $option->id }}" @selected(old('patient_id', optional($patient)->id) == $option->id)>{{ $option->full_name }} ({{ $option->mrn }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Admission type</label>
                <select name="admission_type">@foreach(['emergency','elective','referral','observation','maternity','surgical','other'] as $type)<option value="{{ $type }}" @selected(old('admission_type', 'emergency') === $type)>{{ ucfirst($type) }}</option>@endforeach</select>
            </div>
            <div>
                <label>Admitting doctor</label>
                <select name="admitting_doctor_id"><option value="">Unassigned</option>@foreach($doctors as $doctor)<option value="{{ $doctor->id }}" @selected(old('admitting_doctor_id', optional($visit)->provider_id) == $doctor->id)>{{ $doctor->name }}</option>@endforeach</select>
            </div>
            <div>
                <label>Responsible doctor</label>
                <select name="current_doctor_id"><option value="">Same as admitting</option>@foreach($doctors as $doctor)<option value="{{ $doctor->id }}" @selected(old('current_doctor_id', optional($visit)->provider_id) == $doctor->id)>{{ $doctor->name }}</option>@endforeach</select>
            </div>
            <div>
                <label>Ward</label>
                <select name="ward_id" required>
                    @foreach($wards as $ward)
                        <option value="{{ $ward->id }}" @selected(old('ward_id') == $ward->id)>{{ $ward->name }} ({{ $ward->beds->count() }} available)</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Bed</label>
                <select name="bed_id" required>
                    @foreach($wards as $ward)
                        @foreach($ward->beds as $bed)
                            <option value="{{ $bed->id }}" @selected(old('bed_id') == $bed->id)>{{ $ward->name }} - Bed {{ $bed->bed_number }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <div><label>Admitted at</label><input type="datetime-local" name="admitted_at" value="{{ old('admitted_at', now()->format('Y-m-d\\TH:i')) }}" required></div>
            <div><label>Expected discharge</label><input type="datetime-local" name="expected_discharge_at" value="{{ old('expected_discharge_at') }}"></div>
            <div><label>Payment type</label><input name="payment_type" value="{{ old('payment_type', optional($patient)->insurance_provider ? 'Insurance' : '') }}"></div>
            <div><label>Next of kin</label><input name="next_of_kin_name" value="{{ old('next_of_kin_name') }}"></div>
            <div><label>Next of kin phone</label><input name="next_of_kin_phone" value="{{ old('next_of_kin_phone') }}"></div>
            <div class="field-span-2"><label>Reason for admission</label><textarea name="reason_for_admission" rows="3" required>{{ old('reason_for_admission', optional($visit)->chief_complaint) }}</textarea></div>
            <div class="field-span-2"><label>Provisional diagnosis</label><textarea name="provisional_diagnosis" rows="3">{{ old('provisional_diagnosis') }}</textarea></div>
            <div class="field-span-2"><label>Current diagnosis</label><textarea name="current_diagnosis" rows="3">{{ old('current_diagnosis') }}</textarea></div>
            <div class="field-span-2"><label>Initial care plan</label><textarea name="care_plan" rows="4">{{ old('care_plan') }}</textarea></div>
            <div class="field-span-2"><label>Consent notes</label><textarea name="consent_notes" rows="3">{{ old('consent_notes') }}</textarea></div>
            <div><button class="primary-button" type="submit">Admit patient</button></div>
        </form>
    </div>
@endsection

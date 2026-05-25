@extends('layouts.app')

@section('title', 'New Lab Order')
@section('section', 'Laboratory')
@section('page_title', 'New lab order')

@section('content')
<div class="panel">
    <form action="{{ route('laboratory.store') }}" method="POST" class="form-grid">
        @csrf
        <div>
            <label>Patient</label>
            @if(isset($visit))
                <input type="hidden" name="patient_id" value="{{ $visit->patient_id }}">
                <input type="text" value="{{ $visit->patient->full_name }}" readonly>
            @else
                <select name="patient_id" required>
                    <option value="">Select patient</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->full_name }} ({{ $patient->mrn }})</option>
                    @endforeach
                </select>
            @endif
        </div>
        <div>
            <label>Visit</label>
            <input type="number" name="visit_id" value="{{ old('visit_id', $visit->id ?? request('visit_id')) }}">
        </div>
        <div>
            <label>Test type</label>
            <input list="lab-services" type="text" name="test_type" required>
            <datalist id="lab-services">
                @foreach($services as $service)
                    <option value="{{ $service->name }}" data-price="{{ $service->price }}"></option>
                @endforeach
            </datalist>
        </div>
        <div>
            <label>Price</label>
            <input type="number" name="price" step="0.01" min="0" value="{{ old('price', 0) }}">
        </div>
        <div>
            <label>Ordered at</label>
            <input type="date" name="ordered_at" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="field-span-2">
            <label>Notes</label>
            <textarea name="notes"></textarea>
        </div>
        <div>
            <button type="submit" class="primary-button">Create lab order</button>
        </div>
    </form>
</div>
@endsection

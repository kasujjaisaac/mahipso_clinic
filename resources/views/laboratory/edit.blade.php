@extends('layouts.app')

@section('title', 'Lab Result')
@section('section', 'Laboratory')
@section('page_title', 'Update lab order/result')

@section('content')
<div class="panel">
    <form action="{{ route('laboratory.update', $labTest) }}" method="POST" class="form-grid">
        @csrf
        @method('PUT')
        <input type="hidden" name="patient_id" value="{{ $labTest->patient_id }}">
        <input type="hidden" name="visit_id" value="{{ $labTest->visit_id }}">
        <div>
            <label>Test type</label>
            <input type="text" name="test_type" value="{{ $labTest->test_type }}" required>
        </div>
        <div>
            <label>Status</label>
            <select name="status">
                @foreach(['ordered', 'in_progress', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected($labTest->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Price</label>
            <input type="number" name="price" step="0.01" min="0" value="{{ $labTest->price }}">
        </div>
        <div>
            <label>Ordered at</label>
            <input type="date" name="ordered_at" value="{{ optional($labTest->ordered_at)->format('Y-m-d') ?? $labTest->ordered_at }}" required>
        </div>
        <div>
            <label>Completed at</label>
            <input type="date" name="completed_at" value="{{ optional($labTest->completed_at)->format('Y-m-d') ?? $labTest->completed_at }}">
        </div>
        <div>
            <label>Result flag</label>
            <select name="result_flag">
                <option value="">None</option>
                @foreach(['normal', 'abnormal', 'critical'] as $flag)
                    <option value="{{ $flag }}" @selected($labTest->result_flag === $flag)>{{ ucfirst($flag) }}</option>
                @endforeach
            </select>
        </div>
        <div class="field-span-2">
            <label>Results</label>
            <textarea name="results" rows="5">{{ $labTest->results }}</textarea>
        </div>
        <div class="field-span-2">
            <label>Notes</label>
            <textarea name="notes">{{ $labTest->notes }}</textarea>
        </div>
        <div>
            <button type="submit" class="primary-button">Save result</button>
        </div>
    </form>
</div>
@endsection

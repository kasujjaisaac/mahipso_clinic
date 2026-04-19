@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">New Laboratory Test</h1>
    <form action="{{ route('laboratory.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        @csrf
        <div class="mb-4">
            <label for="patient_id" class="block font-semibold mb-1">Patient</label>
            <input type="number" name="patient_id" id="patient_id" class="form-control" value="{{ old('patient_id', request('patient_id')) }}" required>
        </div>
        <div class="mb-4">
            <label for="visit_id" class="block font-semibold mb-1">Visit (optional)</label>
            <input type="number" name="visit_id" id="visit_id" class="form-control" value="{{ old('visit_id', request('visit_id')) }}">
        </div>
        <div class="mb-4">
            <label for="test_type" class="block font-semibold mb-1">Test Type</label>
            <input type="text" name="test_type" id="test_type" class="form-control" required>
        </div>
        <div class="mb-4">
            <label for="ordered_at" class="block font-semibold mb-1">Ordered At</label>
            <input type="date" name="ordered_at" id="ordered_at" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="mb-4">
            <label for="notes" class="block font-semibold mb-1">Notes</label>
            <textarea name="notes" id="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create Lab Test</button>
    </form>
</div>
@endsection

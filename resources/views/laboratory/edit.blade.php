@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Edit Laboratory Test</h1>
    <form action="{{ route('laboratory.update', $labTest) }}" method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="patient_id" class="block font-semibold mb-1">Patient</label>
            <input type="number" name="patient_id" id="patient_id" class="form-control" value="{{ $labTest->patient_id }}" required>
        </div>
        <div class="mb-4">
            <label for="visit_id" class="block font-semibold mb-1">Visit (optional)</label>
            <input type="number" name="visit_id" id="visit_id" class="form-control" value="{{ $labTest->visit_id }}">
        </div>
        <div class="mb-4">
            <label for="test_type" class="block font-semibold mb-1">Test Type</label>
            <input type="text" name="test_type" id="test_type" class="form-control" value="{{ $labTest->test_type }}" required>
        </div>
        <div class="mb-4">
            <label for="status" class="block font-semibold mb-1">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="ordered" @if($labTest->status=='ordered') selected @endif>Ordered</option>
                <option value="in_progress" @if($labTest->status=='in_progress') selected @endif>In Progress</option>
                <option value="completed" @if($labTest->status=='completed') selected @endif>Completed</option>
                <option value="cancelled" @if($labTest->status=='cancelled') selected @endif>Cancelled</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="ordered_at" class="block font-semibold mb-1">Ordered At</label>
            <input type="date" name="ordered_at" id="ordered_at" class="form-control" value="{{ $labTest->ordered_at }}" required>
        </div>
        <div class="mb-4">
            <label for="completed_at" class="block font-semibold mb-1">Completed At</label>
            <input type="date" name="completed_at" id="completed_at" class="form-control" value="{{ $labTest->completed_at }}">
        </div>
        <div class="mb-4">
            <label for="results" class="block font-semibold mb-1">Results</label>
            <textarea name="results" id="results" class="form-control">{{ $labTest->results }}</textarea>
        </div>
        <div class="mb-4">
            <label for="notes" class="block font-semibold mb-1">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ $labTest->notes }}</textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Lab Test</button>
    </form>
</div>
@endsection

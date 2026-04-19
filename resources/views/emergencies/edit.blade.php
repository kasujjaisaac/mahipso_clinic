@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Edit Emergency</h1>
    <form action="{{ route('emergencies.update', $emergency) }}" method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="patient_id" class="block font-semibold mb-1">Patient ID</label>
            <input type="number" name="patient_id" id="patient_id" class="form-control" value="{{ $emergency->patient_id }}" required>
        </div>
        <div class="mb-4">
            <label for="type" class="block font-semibold mb-1">Type</label>
            <input type="text" name="type" id="type" class="form-control" value="{{ $emergency->type }}" required>
        </div>
        <div class="mb-4">
            <label for="status" class="block font-semibold mb-1">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="open" @if($emergency->status=='open') selected @endif>Open</option>
                <option value="resolved" @if($emergency->status=='resolved') selected @endif>Resolved</option>
                <option value="referred" @if($emergency->status=='referred') selected @endif>Referred</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="reported_at" class="block font-semibold mb-1">Reported At</label>
            <input type="datetime-local" name="reported_at" id="reported_at" class="form-control" value="{{ $emergency->reported_at->format('Y-m-d\TH:i') }}" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block font-semibold mb-1">Description</label>
            <textarea name="description" id="description" class="form-control">{{ $emergency->description }}</textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Emergency</button>
    </form>
</div>
@endsection

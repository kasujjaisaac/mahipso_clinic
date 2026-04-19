@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow rounded-lg p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Emergency Details</h1>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <dt class="font-semibold">Patient</dt>
                <dd>{{ $emergency->patient->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Type</dt>
                <dd>{{ $emergency->type }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Status</dt>
                <dd>{{ ucfirst($emergency->status) }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Reported At</dt>
                <dd>{{ $emergency->reported_at }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-semibold">Description</dt>
                <dd>{{ $emergency->description ?? '—' }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-4">
            <a href="{{ route('emergencies.edit', $emergency) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</a>
            <a href="{{ route('emergencies.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Back to List</a>
        </div>
    </div>
</div>
@endsection

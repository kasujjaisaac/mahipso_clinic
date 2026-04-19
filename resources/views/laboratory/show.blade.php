@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow rounded-lg p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Lab Test Details</h1>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <dt class="font-semibold">Patient</dt>
                <dd>{{ $labTest->patient->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Test Type</dt>
                <dd>{{ $labTest->test_type }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Status</dt>
                <dd>{{ ucfirst($labTest->status) }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Ordered At</dt>
                <dd>{{ $labTest->ordered_at }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Completed At</dt>
                <dd>{{ $labTest->completed_at ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-semibold">Results</dt>
                <dd>{{ $labTest->results ?? 'Pending' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-semibold">Notes</dt>
                <dd>{{ $labTest->notes ?? '—' }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-4">
            <a href="{{ route('laboratory.edit', $labTest) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</a>
            <a href="{{ route('laboratory.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Back to List</a>
        </div>
    </div>
</div>
@endsection

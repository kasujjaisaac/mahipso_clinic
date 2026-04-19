@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Laboratory Tests</h1>
        <a href="{{ route('laboratory.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">New Lab Test</a>
    </div>
    <div class="bg-white shadow rounded-lg p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Patient</th>
                    <th class="px-4 py-2 text-left">Test Type</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Ordered At</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($labTests as $test)
                <tr>
                    <td class="px-4 py-2">{{ $test->id }}</td>
                    <td class="px-4 py-2">{{ $test->patient->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $test->test_type }}</td>
                    <td class="px-4 py-2">{{ ucfirst($test->status) }}</td>
                    <td class="px-4 py-2">{{ $test->ordered_at }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('laboratory.show', $test) }}" class="text-blue-600 hover:underline">View</a>
                        <a href="{{ route('laboratory.edit', $test) }}" class="text-yellow-600 hover:underline ml-2">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $labTests->links() }}
        </div>
    </div>
</div>
@endsection

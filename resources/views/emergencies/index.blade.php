@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Emergencies</h1>
    <a href="{{ route('emergencies.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">Record Emergency</a>
    <div class="bg-white shadow rounded-lg p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Patient</th>
                    <th class="px-4 py-2 text-left">Type</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Reported At</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($emergencies as $emergency)
                <tr>
                    <td class="px-4 py-2">{{ $emergency->patient->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $emergency->type }}</td>
                    <td class="px-4 py-2">{{ ucfirst($emergency->status) }}</td>
                    <td class="px-4 py-2">{{ $emergency->reported_at }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('emergencies.show', $emergency) }}" class="text-blue-600 hover:underline">View</a>
                        <a href="{{ route('emergencies.edit', $emergency) }}" class="text-yellow-600 hover:underline ml-2">Edit</a>
                        <form action="{{ route('emergencies.destroy', $emergency) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline ml-2" onclick="return confirm('Delete this emergency?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $emergencies->links() }}
        </div>
    </div>
</div>
@endsection

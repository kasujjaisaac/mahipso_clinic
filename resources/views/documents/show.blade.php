@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow rounded-lg p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Document Details</h1>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <dt class="font-semibold">Title</dt>
                <dd>{{ $document->title }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Uploaded By</dt>
                <dd>{{ $document->user->name ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Uploaded At</dt>
                <dd>{{ $document->created_at->format('M d, Y H:i') }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-semibold">Description</dt>
                <dd>{{ $document->description ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-semibold">File</dt>
                <dd>
                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-blue-600 hover:underline">Download/View File</a>
                </dd>
            </div>
        </dl>
        <div class="mt-6">
            <a href="{{ route('documents.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Back to List</a>
        </div>
    </div>
</div>
@endsection

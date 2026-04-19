@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow rounded-lg p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Notification Details</h1>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <dt class="font-semibold">Title</dt>
                <dd>{{ $notification->title }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Type</dt>
                <dd>{{ ucfirst($notification->type) }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Scheduled For</dt>
                <dd>{{ $notification->scheduled_for ? $notification->scheduled_for->format('M d, Y H:i') : '—' }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Read</dt>
                <dd>{{ $notification->read_at ? $notification->read_at->format('M d, Y H:i') : 'No' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-semibold">Body</dt>
                <dd>{{ $notification->body }}</dd>
            </div>
        </dl>
        <div class="mt-6">
            <a href="{{ route('notifications.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Back to List</a>
        </div>
    </div>
</div>
@endsection

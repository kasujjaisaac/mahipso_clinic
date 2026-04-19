@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Notifications & Reminders</h1>
    <div class="bg-white shadow rounded-lg p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Title</th>
                    <th class="px-4 py-2 text-left">Type</th>
                    <th class="px-4 py-2 text-left">Scheduled For</th>
                    <th class="px-4 py-2 text-left">Read</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notifications as $notification)
                <tr>
                    <td class="px-4 py-2">{{ $notification->title }}</td>
                    <td class="px-4 py-2">{{ ucfirst($notification->type) }}</td>
                    <td class="px-4 py-2">{{ $notification->scheduled_for ? $notification->scheduled_for->format('M d, Y H:i') : '—' }}</td>
                    <td class="px-4 py-2">{{ $notification->read_at ? 'Yes' : 'No' }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('notifications.show', $notification) }}" class="text-blue-600 hover:underline">View</a>
                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline ml-2" onclick="return confirm('Delete this notification?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection

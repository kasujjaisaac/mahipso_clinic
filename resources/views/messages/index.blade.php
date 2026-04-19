@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Messages</h1>
    <a href="{{ route('messages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">Send Message</a>
    <div class="bg-white shadow rounded-lg p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">From</th>
                    <th class="px-4 py-2 text-left">To</th>
                    <th class="px-4 py-2 text-left">Subject</th>
                    <th class="px-4 py-2 text-left">Sent At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($messages as $message)
                <tr>
                    <td class="px-4 py-2">{{ $message->sender->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $message->recipient->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $message->subject }}</td>
                    <td class="px-4 py-2">{{ $message->created_at }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $messages->links() }}
        </div>
    </div>
</div>
@endsection

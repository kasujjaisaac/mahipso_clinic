@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        <h1 class="text-2xl font-bold mb-2">{{ $message->subject }}</h1>
        <div class="mb-2 text-gray-600 text-sm">
            <span>From: <strong>{{ $message->sender->name ?? 'N/A' }}</strong></span>
            <span class="ml-4">To: <strong>{{ $message->recipient->name ?? 'N/A' }}</strong></span>
        </div>
        <div class="mb-4 text-gray-500 text-xs">Sent: {{ $message->created_at }}</div>
        <div class="mb-6">
            <p>{{ $message->body }}</p>
        </div>
        <a href="{{ route('messages.index') }}" class="text-blue-600 hover:underline">&larr; Back to Messages</a>
    </div>
</div>
@endsection

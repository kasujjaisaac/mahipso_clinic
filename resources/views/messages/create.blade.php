@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Send Message</h1>
    <form action="{{ route('messages.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        @csrf
        <div class="mb-4">
            <label for="recipient_id" class="block font-semibold mb-1">Recipient</label>
            <select name="recipient_id" id="recipient_id" class="form-control" required>
                <option value="">Select User</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label for="subject" class="block font-semibold mb-1">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control" required>
        </div>
        <div class="mb-4">
            <label for="body" class="block font-semibold mb-1">Message</label>
            <textarea name="body" id="body" class="form-control" required></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Send</button>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Create Notification</h1>
    <form action="{{ route('notifications.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        @csrf
        <div class="mb-4">
            <label for="user_id" class="block font-semibold mb-1">User (optional)</label>
            <input type="number" name="user_id" id="user_id" class="form-control" placeholder="User ID (leave blank for all)">
        </div>
        <div class="mb-4">
            <label for="title" class="block font-semibold mb-1">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="mb-4">
            <label for="body" class="block font-semibold mb-1">Body</label>
            <textarea name="body" id="body" class="form-control" required></textarea>
        </div>
        <div class="mb-4">
            <label for="type" class="block font-semibold mb-1">Type</label>
            <select name="type" id="type" class="form-control">
                <option value="info">Info</option>
                <option value="reminder">Reminder</option>
                <option value="alert">Alert</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="scheduled_for" class="block font-semibold mb-1">Scheduled For (optional)</label>
            <input type="datetime-local" name="scheduled_for" id="scheduled_for" class="form-control">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create Notification</button>
    </form>
</div>
@endsection

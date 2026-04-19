@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow rounded-lg p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-4">Role Details</h1>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <dt class="font-semibold">Name</dt>
                <dd>{{ $role->name }}</dd>
            </div>
            <div>
                <dt class="font-semibold">Description</dt>
                <dd>{{ $role->description ?? '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="font-semibold">Users</dt>
                <dd>
                    @foreach($role->users as $user)
                        <span class="badge bg-secondary">{{ $user->name }}</span>
                    @endforeach
                </dd>
            </div>
        </dl>
        <div class="mt-6 flex gap-4">
            <a href="{{ route('roles.edit', $role) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</a>
            <a href="{{ route('roles.index') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Back to List</a>
        </div>
    </div>
</div>
@endsection

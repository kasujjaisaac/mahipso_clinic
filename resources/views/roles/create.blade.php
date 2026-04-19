@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Add Role</h1>
    <form action="{{ route('roles.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        @csrf
        <div class="mb-4">
            <label for="name" class="block font-semibold mb-1">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block font-semibold mb-1">Description</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Role</button>
    </form>
</div>
@endsection

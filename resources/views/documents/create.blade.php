@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Upload Document</h1>
    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        @csrf
        <div class="mb-4">
            <label for="title" class="block font-semibold mb-1">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="mb-4">
            <label for="file" class="block font-semibold mb-1">File</label>
            <input type="file" name="file" id="file" class="form-control" required>
        </div>
        <div class="mb-4">
            <label for="description" class="block font-semibold mb-1">Description</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload</button>
    </form>
</div>
@endsection

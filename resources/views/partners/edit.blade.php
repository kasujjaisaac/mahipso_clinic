@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Edit Partner</h1>
    <form action="{{ route('partners.update', $partner) }}" method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block font-semibold mb-1">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $partner->name }}" required>
        </div>
        <div class="mb-4">
            <label for="contact_person" class="block font-semibold mb-1">Contact Person</label>
            <input type="text" name="contact_person" id="contact_person" class="form-control" value="{{ $partner->contact_person }}">
        </div>
        <div class="mb-4">
            <label for="email" class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $partner->email }}">
        </div>
        <div class="mb-4">
            <label for="phone" class="block font-semibold mb-1">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ $partner->phone }}">
        </div>
        <div class="mb-4">
            <label for="address" class="block font-semibold mb-1">Address</label>
            <input type="text" name="address" id="address" class="form-control" value="{{ $partner->address }}">
        </div>
        <div class="mb-4">
            <label for="notes" class="block font-semibold mb-1">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ $partner->notes }}</textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Partner</button>
    </form>
</div>
@endsection

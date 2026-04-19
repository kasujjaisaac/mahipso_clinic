@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-4">Add Partner</h1>
    <form action="{{ route('partners.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 max-w-lg mx-auto">
        @csrf
        <div class="mb-4">
            <label for="name" class="block font-semibold mb-1">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-4">
            <label for="contact_person" class="block font-semibold mb-1">Contact Person</label>
            <input type="text" name="contact_person" id="contact_person" class="form-control">
        </div>
        <div class="mb-4">
            <label for="email" class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" id="email" class="form-control">
        </div>
        <div class="mb-4">
            <label for="phone" class="block font-semibold mb-1">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control">
        </div>
        <div class="mb-4">
            <label for="address" class="block font-semibold mb-1">Address</label>
            <input type="text" name="address" id="address" class="form-control">
        </div>
        <div class="mb-4">
            <label for="notes" class="block font-semibold mb-1">Notes</label>
            <textarea name="notes" id="notes" class="form-control"></textarea>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Partner</button>
    </form>
</div>
@endsection

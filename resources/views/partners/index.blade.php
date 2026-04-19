@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Partners</h1>
    <a href="{{ route('partners.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">Add Partner</a>
    <div class="bg-white shadow rounded-lg p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Contact Person</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Phone</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($partners as $partner)
                <tr>
                    <td class="px-4 py-2">{{ $partner->name }}</td>
                    <td class="px-4 py-2">{{ $partner->contact_person }}</td>
                    <td class="px-4 py-2">{{ $partner->email }}</td>
                    <td class="px-4 py-2">{{ $partner->phone }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('partners.show', $partner) }}" class="text-blue-600 hover:underline">View</a>
                        <a href="{{ route('partners.edit', $partner) }}" class="text-yellow-600 hover:underline ml-2">Edit</a>
                        <form action="{{ route('partners.destroy', $partner) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline ml-2" onclick="return confirm('Delete this partner?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $partners->links() }}
        </div>
    </div>
</div>
@endsection

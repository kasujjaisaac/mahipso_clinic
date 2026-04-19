@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">User Roles</h1>
    <a href="{{ route('roles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4 inline-block">Add Role</a>
    <div class="bg-white shadow rounded-lg p-4">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Description</th>
                    <th class="px-4 py-2 text-left">Users</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td class="px-4 py-2">{{ $role->name }}</td>
                    <td class="px-4 py-2">{{ $role->description }}</td>
                    <td class="px-4 py-2">{{ $role->users->count() }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('roles.show', $role) }}" class="text-blue-600 hover:underline">View</a>
                        <a href="{{ route('roles.edit', $role) }}" class="text-yellow-600 hover:underline ml-2">Edit</a>
                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline ml-2" onclick="return confirm('Delete this role?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    </div>
</div>
@endsection

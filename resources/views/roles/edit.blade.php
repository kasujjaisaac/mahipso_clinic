@extends('layouts.app')

@section('title', 'Edit Role')
@section('page_title', 'Edit Role')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('roles.show', $role) }}">Role details</a>
    <a class="ghost-button" href="{{ route('roles.index') }}">Roles</a>
@endsection

@section('content')
<div class="panel">
    <form method="POST" action="{{ route('roles.update', $role) }}" class="form-grid">
        @csrf
        @method('PUT')
        <div class="field">
            <label for="name">Role Name</label>
            <input id="name" name="name" value="{{ old('name', $role->name) }}" required>
            @error('name')<p class="subtle">{{ $message }}</p>@enderror
        </div>
        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="2">{{ old('description', $role->description) }}</textarea>
            @error('description')<p class="subtle">{{ $message }}</p>@enderror
        </div>
        <div class="field-span-2">
            <label>Office Modules</label>
            <div class="card-grid">
                @foreach($modules as $key => $module)
                    <label class="info-card" style="cursor:pointer;">
                        <input type="checkbox" name="module_access[]" value="{{ $key }}" {{ in_array($key, old('module_access', $selectedModules), true) ? 'checked' : '' }}>
                        <strong>{{ $module['label'] }}</strong>
                        <p>{{ $module['description'] }}</p>
                    </label>
                @endforeach
            </div>
            @error('module_access')<p class="subtle">{{ $message }}</p>@enderror
        </div>
        <div>
            <button class="primary-button" type="submit">Save role access</button>
        </div>
    </form>
</div>
@endsection

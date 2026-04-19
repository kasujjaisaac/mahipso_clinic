@extends('layouts.app')

@section('title', 'Edit Branch')
@section('section', 'Administration')
@section('kicker', 'Branch Setup')
@section('page_title', 'Edit ' . $branch->name)
@section('page_subtitle', 'Update branch information and operational status for this clinic location.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('branches.index') }}">Back to branches</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('branches.update', $branch) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label for="name">Branch name</label>
                    <input id="name" name="name" value="{{ old('name', $branch->name) }}" required>
                </div>
                <div class="field">
                    <label for="code">Code</label>
                    <input id="code" name="code" value="{{ old('code', $branch->code) }}" required>
                </div>
                <div class="field field-span-2">
                    <label for="address">Address</label>
                    <input id="address" name="address" value="{{ old('address', $branch->address) }}">
                </div>
                <div class="field">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone', $branch->phone) }}">
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $branch->email) }}">
                </div>
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active" {{ old('status', $branch->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $branch->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="action-stack" style="margin-top: 1.25rem;">
                <button class="primary-button" type="submit">Update branch</button>
                <a class="ghost-button" href="{{ route('branches.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Create Branch')
@section('section', 'Administration')
@section('kicker', 'Branch Setup')
@section('page_title', 'Create a new branch')
@section('page_subtitle', 'Add a clinic location so it can be assigned to users, patients, appointments, and records.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('branches.index') }}">Back to branches</a>
@endsection

@section('content')
    <div class="panel">
        <form method="POST" action="{{ route('branches.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label for="name">Branch name</label>
                    <input id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="field">
                    <label for="code">Code</label>
                    <input id="code" name="code" value="{{ old('code') }}" required>
                </div>
                <div class="field field-span-2">
                    <label for="address">Address</label>
                    <input id="address" name="address" value="{{ old('address') }}">
                </div>
                <div class="field">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}">
                </div>
                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}">
                </div>
                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="action-stack" style="margin-top: 1.25rem;">
                <button class="primary-button" type="submit">Save branch</button>
                <a class="ghost-button" href="{{ route('branches.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Branch Details')
@section('section', 'Administration')
@section('kicker', 'Branch Profile')
@section('page_title', $branch->name)
@section('page_subtitle', 'Reference information for this clinic branch, including identity, status, and contact details.')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('branches.index') }}">Back</a>
    <a class="primary-button" href="{{ route('branches.edit', $branch) }}">Edit branch</a>
@endsection

@section('content')
    <div class="panel">
        <div class="detail-grid">
            <div class="detail-item"><span class="detail-label">ID</span><div class="detail-value">{{ $branch->id }}</div></div>
            <div class="detail-item"><span class="detail-label">Code</span><div class="detail-value">{{ $branch->code }}</div></div>
            <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value"><span class="status-pill {{ $branch->status }}">{{ ucfirst($branch->status) }}</span></div></div>
            <div class="detail-item"><span class="detail-label">Phone</span><div class="detail-value">{{ $branch->phone ?: 'Not provided' }}</div></div>
            <div class="detail-item"><span class="detail-label">Email</span><div class="detail-value">{{ $branch->email ?: 'Not provided' }}</div></div>
            <div class="detail-item"><span class="detail-label">Address</span><div class="detail-value">{{ $branch->address ?: 'Not provided' }}</div></div>
            <div class="detail-item"><span class="detail-label">City</span><div class="detail-value">{{ $branch->city ?: 'Not provided' }}</div></div>
            <div class="detail-item"><span class="detail-label">State</span><div class="detail-value">{{ $branch->state ?: 'Not provided' }}</div></div>
            <div class="detail-item"><span class="detail-label">Country</span><div class="detail-value">{{ $branch->country ?: 'Not provided' }}</div></div>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Branches')
@section('section', 'Administration')
@section('kicker', 'Clinic Branches')
@section('page_title', 'Branch management')
@section('page_subtitle', 'Create, review, and update clinic locations that power the rest of the system.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('branches.create') }}">New branch</a>
@endsection

@section('content')
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">All branches</h2>
                <p class="subtle">Active and inactive clinic locations available inside the platform.</p>
            </div>
        </div>

        <div class="card-grid">
            @foreach($branches as $branch)
                <div class="entity-card">
                    <div class="inline-actions" style="justify-content: space-between;">
                        <span class="chip">#{{ $branch->id }}</span>
                        <span class="status-pill {{ $branch->status }}">{{ ucfirst($branch->status) }}</span>
                    </div>
                    <h3 style="margin-top: 0.85rem;">{{ $branch->name }}</h3>
                    <p>{{ $branch->address ?: 'Address not set yet.' }}</p>
                    <div class="inline-actions" style="margin-top: 1rem;">
                        <a class="ghost-button" href="{{ route('branches.show', $branch) }}">View</a>
                        <a class="primary-button" href="{{ route('branches.edit', $branch) }}">Edit</a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination-wrap">{{ $branches->links() }}</div>
    </div>
@endsection

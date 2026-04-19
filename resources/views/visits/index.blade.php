@extends('layouts.app')

@section('title', 'Visits')
@section('section', 'Clinical Operations')
@section('kicker', 'Visit Desk')
@section('page_title', 'Visits')
@section('page_subtitle', 'Follow patient encounters from check-in to closure, including provider assignment and linked records.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('visits.create') }}">New visit</a>
@endsection

@section('content')
    <div class="panel">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Visit tracker</h2>
                <p class="table-meta">Search visits by patient and review activity, type, provider, and status.</p>
            </div>
            <form class="toolbar-form" method="GET" action="{{ route('visits.index') }}">
                @if (request('branch'))
                    <input type="hidden" name="branch" value="{{ request('branch') }}">
                @endif
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by patient name or MRN">
                <button class="ghost-button" type="submit">Search</button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Visit Date</th>
                        <th>Type</th>
                        <th>Provider</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visits as $visit)
                        <tr>
                            <td>#{{ $visit->id }}</td>
                            <td><strong>{{ $visit->patient->full_name ?? 'N/A' }}</strong></td>
                            <td>{{ $visit->visit_date->format('Y-m-d H:i') }}</td>
                            <td>{{ ucfirst($visit->visit_type) }}</td>
                            <td>{{ optional($visit->provider)->name ?: 'N/A' }}</td>
                            <td><span class="status-pill {{ $visit->status }}">{{ ucfirst($visit->status) }}</span></td>
                            <td>
                                <div class="inline-actions">
                                    <a class="ghost-button" href="{{ route('visits.show', $visit) }}">View</a>
                                    <a class="primary-button" href="{{ route('visits.edit', $visit) }}">Edit</a>
                                    <form method="POST" action="{{ route('visits.destroy', $visit) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="danger-button" type="submit" onclick="return confirm('Delete visit?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">No visits found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $visits->links() }}</div>
    </div>
@endsection

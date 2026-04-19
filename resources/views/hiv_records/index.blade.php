@extends('layouts.app')

@section('title', 'HIV Records')
@section('section', 'Clinical Records')
@section('kicker', 'HIV Monitoring')
@section('page_title', 'HIV records')
@section('page_subtitle', 'Manage HIV-related tests, adherence notes, ART status, and regimen information.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('hiv-records.create') }}">New HIV record</a>
@endsection

@section('content')
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">HIV monitoring records</h2>
                <p class="table-meta">A structured view of tests, outcomes, and treatment support details tied to visits.</p>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Visit</th>
                        <th>Patient</th>
                        <th>Test</th>
                        <th>Result</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hivRecords as $record)
                        <tr>
                            <td>#{{ $record->id }}</td>
                            <td>#{{ $record->visit->id ?? 'N/A' }}</td>
                            <td>{{ $record->patient->full_name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($record->test_type) }}</td>
                            <td><span class="status-pill {{ $record->test_result }}">{{ ucfirst($record->test_result) }}</span></td>
                            <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="inline-actions">
                                    <a class="ghost-button" href="{{ route('hiv-records.show', $record) }}">Show</a>
                                    <a class="primary-button" href="{{ route('hiv-records.edit', $record) }}">Edit</a>
                                    <form method="POST" action="{{ route('hiv-records.destroy', $record) }}" onsubmit="return confirm('Delete record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="danger-button" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">No HIV records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $hivRecords->links() }}</div>
    </div>
@endsection

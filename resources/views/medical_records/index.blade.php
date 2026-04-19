@extends('layouts.app')

@section('title', 'Medical Records')
@section('section', 'Clinical Records')
@section('kicker', 'Medical Notes')
@section('page_title', 'Medical records')
@section('page_subtitle', 'Review provider-written clinical records, diagnoses, treatment plans, and supporting notes.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('medical-records.create') }}">Create record</a>
@endsection

@section('content')
    <div class="panel">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Clinical records</h2>
                <p class="table-meta">Patient-linked records with visit context, provider information, and clinical summaries.</p>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Visit</th>
                        <th>Patient</th>
                        <th>Provider</th>
                        <th>Diagnosis</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicalRecords as $record)
                        <tr>
                            <td>#{{ $record->id }}</td>
                            <td>#{{ $record->visit->id ?? 'N/A' }}</td>
                            <td>{{ $record->patient->full_name ?? ($record->visit->patient->full_name ?? 'N/A') }}</td>
                            <td>{{ $record->provider->name ?? 'N/A' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($record->diagnosis, 60) ?: 'No diagnosis recorded' }}</td>
                            <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="inline-actions">
                                    <a class="ghost-button" href="{{ route('medical-records.show', $record) }}">Show</a>
                                    <a class="primary-button" href="{{ route('medical-records.edit', $record) }}">Edit</a>
                                    <form method="POST" action="{{ route('medical-records.destroy', $record) }}" onsubmit="return confirm('Delete this record?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="danger-button" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">No medical records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $medicalRecords->links() }}</div>
    </div>
@endsection

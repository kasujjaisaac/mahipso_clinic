@extends('layouts.app')

@section('title', 'Staff Appraisals')
@section('page_title', 'Staff appraisals')
@section('page_subtitle', 'Track performance reviews, ratings, goals, and improvement areas.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('appraisals.create') }}">New appraisal</a>
@endsection

@section('content')
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Employee</th><th>Period</th><th>Reviewer</th><th>Score</th><th>Rating</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($appraisals as $appraisal)
                    <tr>
                        <td>{{ $appraisal->employee->first_name ?? '' }} {{ $appraisal->employee->last_name ?? '' }}</td>
                        <td>{{ $appraisal->period_start?->format('Y-m-d') }} to {{ $appraisal->period_end?->format('Y-m-d') }}</td>
                        <td>{{ $appraisal->reviewer->name ?? '-' }}</td>
                        <td>{{ $appraisal->score !== null ? number_format($appraisal->score, 2) : '-' }}</td>
                        <td>{{ $appraisal->rating ?? '-' }}</td>
                        <td><span class="status-pill {{ $appraisal->status }}">{{ ucfirst($appraisal->status) }}</span></td>
                        <td><a class="badge-link" href="{{ route('appraisals.show', $appraisal) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-state">No appraisals yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $appraisals->links() }}</div>
</div>
@endsection

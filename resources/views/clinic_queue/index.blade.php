@extends('layouts.app')

@section('title', 'Clinic Queue')
@section('section', 'Clinical Operations')
@section('kicker', 'Patient Flow')
@section('page_title', 'Clinic queue')
@section('page_subtitle', 'Move patients through reception, triage, consultation, lab, pharmacy, billing, and completion.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('visits.create') }}">New visit</a>
    <a class="ghost-button" href="{{ route('visits.index') }}">All visits</a>
@endsection

@section('content')
    <div class="stats-grid">
        @foreach($stages as $stage => $label)
            <div class="metric-card" style="--accent: {{ $stage === 'completed' ? '#2f7d57' : '#b8342b' }};">
                <div class="metric-icon">{{ $loop->iteration }}</div>
                <div>
                    <div class="metric-value">{{ $stageCounts[$stage] ?? 0 }}</div>
                    <div class="metric-label">{{ $label }}</div>
                </div>
            </div>
        @endforeach
    </div>

    @foreach($stages as $stage => $label)
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h2 class="section-title">{{ $label }}</h2>
                    <p class="table-meta">{{ $stageCounts[$stage] ?? 0 }} active visit{{ ($stageCounts[$stage] ?? 0) === 1 ? '' : 's' }}</p>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Visit</th>
                            <th>Patient</th>
                            <th>Provider</th>
                            <th>Checked In</th>
                            <th>Vitals</th>
                            <th>Actions</th>
                            <th>Move To</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitsByStage->get($stage, collect()) as $visit)
                            <tr>
                                <td><a href="{{ route('visits.show', $visit) }}">#{{ $visit->id }}</a></td>
                                <td><strong>{{ $visit->patient->full_name ?? 'N/A' }}</strong><br><span class="subtle">{{ $visit->patient->mrn ?? '' }}</span></td>
                                <td>{{ optional($visit->provider)->name ?: 'Unassigned' }}</td>
                                <td>{{ optional($visit->checked_in_at ?? $visit->visit_date)->format('Y-m-d H:i') }}</td>
                                <td>{{ $visit->vitalSigns ? 'Recorded' : 'Pending' }}</td>
                                <td>
                                    <a class="ghost-button" href="{{ route('visits.show', $visit) }}">Open</a>
                                    @if(! $visit->vitalSigns)
                                        <a class="ghost-button" href="{{ route('vitals.create', $visit) }}">Triage</a>
                                    @endif
                                    <a class="ghost-button" href="{{ route('admissions.create', ['visit_id' => $visit->id]) }}">Admit</a>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('visits.workflow.update', $visit) }}" class="toolbar-form">
                                        @csrf
                                        @method('PATCH')
                                        <select name="workflow_stage">
                                            @foreach($stages as $nextStage => $nextLabel)
                                                <option value="{{ $nextStage }}" {{ $nextStage === $visit->workflow_stage ? 'selected' : '' }}>{{ $nextLabel }}</option>
                                            @endforeach
                                        </select>
                                        <button class="ghost-button" type="submit">Move</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">No patients in this queue.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection

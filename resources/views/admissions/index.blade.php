@extends('layouts.app')

@section('title', 'Admissions')
@section('section', 'Inpatient Management')
@section('kicker', 'Admissions')
@section('page_title', 'Inpatient admissions')
@section('page_subtitle', 'Monitor admitted patients, discharge readiness, ward assignment, and clearance status.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('admissions.create') }}">Admit patient</a>
    <a class="ghost-button" href="{{ route('wards.index') }}">Bed board</a>
@endsection

@section('content')
    <div class="panel">
        <form method="GET" action="{{ route('admissions.index') }}" class="toolbar-form">
            <input name="search" value="{{ request('search') }}" placeholder="Search patient or MRN">
            <select name="status">
                <option value="">All statuses</option>
                @foreach(['admitted','ready_for_discharge','pending_clearance','discharged','transferred','deceased','absconded'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <button class="ghost-button" type="submit">Filter</button>
        </form>
    </div>

    <div class="panel">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Admission</th><th>Patient</th><th>Ward / Bed</th><th>Doctor</th><th>Status</th><th>Length</th><th></th></tr></thead>
                <tbody>
                    @forelse($admissions as $admission)
                        <tr>
                            <td><a href="{{ route('admissions.show', $admission) }}">{{ $admission->admission_no }}</a><br><span class="subtle">{{ $admission->admitted_at->format('Y-m-d H:i') }}</span></td>
                            <td><strong>{{ $admission->patient->full_name }}</strong><br><span class="subtle">{{ $admission->patient->mrn }}</span></td>
                            <td>{{ $admission->ward->name }} / {{ $admission->bed->bed_number }}</td>
                            <td>{{ optional($admission->currentDoctor)->name ?: optional($admission->admittingDoctor)->name ?: 'Unassigned' }}</td>
                            <td><span class="status-pill {{ $admission->status }}">{{ $admission->status_label }}</span></td>
                            <td>{{ $admission->length_of_stay }} day{{ $admission->length_of_stay === 1 ? '' : 's' }}</td>
                            <td><a class="ghost-button" href="{{ route('admissions.show', $admission) }}">Open chart</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty-state">No admissions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $admissions->links() }}
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Appointments')
@section('section', 'Scheduling')
@section('kicker', 'Appointment Desk')
@section('page_title', 'Appointments')
@section('page_subtitle', 'Track booked consultations, assigned doctors, and appointment statuses across your clinic workflows.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('appointments.create') }}">Create appointment</a>
@endsection

@section('content')
    @push('styles')
    <style>
        .status-pill {
            display: inline-block;
            padding: 0.18em 0.7em;
            border-radius: 12px;
            font-size: 0.85em;
            color: #fff;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status-scheduled { background: #3b82f6; }
        .status-confirmed { background: #2563eb; }
        .status-checked_in { background: #f59e42; }
        .status-completed { background: #22c55e; }
        .status-canceled, .status-no_show { background: #ef4444; }
    </style>
    @endpush
    <div class="panel">
        <div class="table-toolbar">
            <div>
                <h2 class="section-title">Appointment schedule</h2>
                <p class="table-meta">Search by patient name or MRN and manage the full appointment lifecycle.</p>
            </div>
            <form class="toolbar-form" method="GET" action="{{ route('appointments.index') }}" style="flex-wrap: nowrap; align-items: center; overflow-x: auto;">
                @if (request('branch'))
                    <input type="hidden" name="branch" value="{{ request('branch') }}">
                @endif
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search patient name or MRN">
                <select name="doctor_id" style="margin-left: 0.5rem;">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(request('doctor_id') == $doctor->id)>{{ $doctor->name }}</option>
                    @endforeach
                </select>
                <select name="status" style="margin-left: 0.5rem;">
                    <option value="">All Statuses</option>
                    @foreach(App\Models\Appointment::statusOptions() as $status)
                        <option value="{{ $status }}" @selected(request('status') == $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
                <input type="date" name="start_date" value="{{ request('start_date') }}" style="margin-left: 0.5rem;" title="From">
                <input type="date" name="end_date" value="{{ request('end_date') }}" style="margin-left: 0.5rem;" title="To">
                <button class="ghost-button" type="submit">Filter</button>
            </form>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Scheduled At</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appointment)
                        <tr>
                            <td>#{{ $appointment->id }}</td>
                            <td><strong>{{ $appointment->patient->full_name ?? 'N/A' }}</strong></td>
                            <td>{{ optional($appointment->doctor)->name ?: 'Unassigned' }}</td>
                            <td>{{ $appointment->scheduled_at->format('Y-m-d H:i') }}</td>
                            <td><span class="status-pill status-{{ $appointment->status }}">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</span></td>
                            <td>
                                <div class="inline-actions">
                                    <a class="ghost-button" href="{{ route('appointments.show', $appointment) }}">View</a>
                                    <a class="primary-button" href="{{ route('appointments.edit', $appointment) }}">Edit</a>
                                    <form method="POST" action="{{ route('appointments.destroy', $appointment) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="danger-button" type="submit" onclick="return confirm('Delete appointment?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">No appointments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrap">{{ $appointments->links() }}</div>
    </div>
@endsection

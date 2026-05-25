@extends('layouts.app')
@section('title', 'Reception Dashboard')
@section('page_title', 'Reception dashboard')
@section('content')
<div class="panel">
    <h2 class="section-title">Today’s appointments</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Time</th><th>Patient</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($appointments as $appointment)
                    <tr><td>{{ $appointment->scheduled_at->format('H:i') }}</td><td>{{ $appointment->patient->full_name ?? 'N/A' }}</td><td>{{ $appointment->status_label }}</td></tr>
                @empty
                    <tr><td colspan="3" class="empty-state">No appointments today.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="panel">
    <h2 class="section-title">Outstanding bills</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Bill</th><th>Patient</th><th>Balance</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($bills as $bill)
                    <tr><td>#{{ $bill->id }}</td><td>{{ $bill->patient->full_name ?? 'N/A' }}</td><td>USh {{ number_format($bill->balance, 0) }}</td><td><a class="ghost-button" href="{{ route('billing.show', $bill) }}">Open</a></td></tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No outstanding bills.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

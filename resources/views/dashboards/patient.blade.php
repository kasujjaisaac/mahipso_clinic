@extends('layouts.app')

@section('title', 'Patient Portal')
@section('page_title', 'Patient portal')

@section('content')
@if(!$patient)
    <div class="panel">
        <div class="empty-state">No patient profile is linked to this login yet. Please contact reception.</div>
    </div>
@else
    <div class="stats-grid">
        <div class="metric-card"><div class="metric-icon">V</div><div><div class="metric-value">{{ $patient->visits->count() }}</div><div class="metric-label">Visits</div></div></div>
        <div class="metric-card"><div class="metric-icon">L</div><div><div class="metric-value">{{ $patient->labTests->count() }}</div><div class="metric-label">Lab tests</div></div></div>
        <div class="metric-card"><div class="metric-icon">B</div><div><div class="metric-value">{{ $bills->count() }}</div><div class="metric-label">Bills</div></div></div>
    </div>
    <div class="panel">
        <h2 class="section-title">Recent bills</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Bill</th><th>Amount</th><th>Paid</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr><td>#{{ $bill->id }}</td><td>USh {{ number_format($bill->amount, 0) }}</td><td>USh {{ number_format($bill->paid, 0) }}</td><td>{{ ucfirst($bill->status) }}</td></tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">No bills found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

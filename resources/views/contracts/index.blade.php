@extends('layouts.app')

@section('title', 'Staff Contracts')
@section('page_title', 'Staff contracts')
@section('page_subtitle', 'Manage employment agreements, contract terms, salaries, and expiry status.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('contracts.create') }}">New contract</a>
@endsection

@section('content')
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Contract No</th><th>Employee</th><th>Type</th><th>Start</th><th>End</th><th>Salary</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($contracts as $contract)
                    <tr>
                        <td>{{ $contract->contract_no }}</td>
                        <td>{{ $contract->employee->first_name ?? '' }} {{ $contract->employee->last_name ?? '' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $contract->contract_type)) }}</td>
                        <td>{{ $contract->start_date?->format('Y-m-d') }}</td>
                        <td>{{ $contract->end_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ number_format($contract->salary_amount, 2) }}</td>
                        <td><span class="status-pill {{ $contract->status }}">{{ ucfirst($contract->status) }}</span></td>
                        <td><a class="badge-link" href="{{ route('contracts.show', $contract) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="empty-state">No contracts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $contracts->links() }}</div>
</div>
@endsection

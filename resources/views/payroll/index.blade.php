@extends('layouts.app')

@section('title', 'Payroll')
@section('page_title', 'Payroll')
@section('page_subtitle', 'Prepare, approve, and mark monthly staff payroll runs as paid.')

@section('topbar_actions')
    <a class="primary-button" href="{{ route('payroll.create') }}">Prepare payroll</a>
@endsection

@section('content')
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Month</th><th>Branch</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($payrollRuns as $payroll)
                    <tr>
                        <td>{{ $payroll->period_month?->format('F Y') }}</td>
                        <td>{{ $payroll->branch->name ?? 'All branches' }}</td>
                        <td>{{ number_format($payroll->gross_total, 2) }}</td>
                        <td>{{ number_format($payroll->deductions_total, 2) }}</td>
                        <td>{{ number_format($payroll->net_total, 2) }}</td>
                        <td><span class="status-pill {{ $payroll->status }}">{{ ucfirst($payroll->status) }}</span></td>
                        <td><a class="badge-link" href="{{ route('payroll.show', $payroll) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-state">No payroll runs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $payrollRuns->links() }}</div>
</div>
@endsection

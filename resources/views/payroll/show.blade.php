@extends('layouts.app')

@section('title', 'Payroll Run')
@section('page_title', 'Payroll - ' . $payroll->period_month->format('F Y'))
@section('page_subtitle', ($payroll->branch->name ?? 'All branches') . ' payroll run')

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('payroll.index') }}">Back to payroll</a>
    @if($payroll->status === 'draft')
        <form method="POST" action="{{ route('payroll.approve', $payroll) }}">@csrf <button class="primary-button" type="submit">Approve</button></form>
    @elseif($payroll->status === 'approved')
        <form method="POST" action="{{ route('payroll.paid', $payroll) }}">@csrf <button class="primary-button" type="submit">Mark paid</button></form>
    @endif
@endsection

@section('content')
<div class="panel">
    <div class="detail-grid">
        <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value">{{ ucfirst($payroll->status) }}</div></div>
        <div class="detail-item"><span class="detail-label">Gross</span><div class="detail-value">{{ number_format($payroll->gross_total, 2) }}</div></div>
        <div class="detail-item"><span class="detail-label">Deductions</span><div class="detail-value">{{ number_format($payroll->deductions_total, 2) }}</div></div>
        <div class="detail-item"><span class="detail-label">Net Pay</span><div class="detail-value">{{ number_format($payroll->net_total, 2) }}</div></div>
        <div class="detail-item"><span class="detail-label">Prepared By</span><div class="detail-value">{{ $payroll->preparedBy->name ?? '-' }}</div></div>
        <div class="detail-item"><span class="detail-label">Approved By</span><div class="detail-value">{{ $payroll->approvedBy->name ?? '-' }}</div></div>
    </div>
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Employee</th><th>Basic</th><th>Allowances</th><th>Deductions</th><th>Net</th><th>Notes</th><th></th></tr></thead>
            <tbody>
                @forelse($payroll->items as $item)
                    <tr>
                        <form method="POST" action="{{ route('payroll.items.update', $payroll) }}">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <td>{{ $item->employee->first_name }} {{ $item->employee->last_name }}<div class="subtle">{{ $item->employee->employee_no }}</div></td>
                            <td><input name="basic_pay" type="number" step="0.01" min="0" value="{{ $item->basic_pay }}" {{ $payroll->status !== 'draft' ? 'readonly' : '' }}></td>
                            <td><input name="allowances" type="number" step="0.01" min="0" value="{{ $item->allowances }}" {{ $payroll->status !== 'draft' ? 'readonly' : '' }}></td>
                            <td><input name="deductions" type="number" step="0.01" min="0" value="{{ $item->deductions }}" {{ $payroll->status !== 'draft' ? 'readonly' : '' }}></td>
                            <td>{{ number_format($item->net_pay, 2) }}</td>
                            <td><input name="notes" value="{{ $item->notes }}" {{ $payroll->status !== 'draft' ? 'readonly' : '' }}></td>
                            <td>
                                @if($payroll->status === 'draft')
                                    <button class="badge-link" type="submit">Save</button>
                                @else
                                    -
                                @endif
                            </td>
                        </form>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-state">No active employees were found for this payroll.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

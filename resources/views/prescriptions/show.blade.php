@extends('layouts.app')

@section('title', 'Prescription #' . $order->id)
@section('section', 'Pharmacy')
@section('page_title', 'Prescription #' . $order->id)

@section('topbar_actions')
    <a class="ghost-button" href="{{ route('prescriptions.index') }}">Back</a>
@endsection

@section('content')
<div class="panel">
    <div class="detail-grid">
        <div class="detail-item"><span class="detail-label">Patient</span><div class="detail-value">{{ $order->patient->full_name ?? 'N/A' }}</div></div>
        <div class="detail-item"><span class="detail-label">Provider</span><div class="detail-value">{{ $order->provider->name ?? 'N/A' }}</div></div>
        <div class="detail-item"><span class="detail-label">Status</span><div class="detail-value">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</div></div>
        <div class="detail-item"><span class="detail-label">Ordered</span><div class="detail-value">{{ optional($order->ordered_at)->format('Y-m-d H:i') }}</div></div>
    </div>
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Medicine</th><th>Qty</th><th>Dosage</th><th>Frequency</th><th>Duration</th><th>Total</th></tr></thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Product' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->dosage }}</td>
                        <td>{{ $item->frequency }}</td>
                        <td>{{ $item->duration }}</td>
                        <td>USh {{ number_format($item->total_price, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if(in_array($order->status, ['pending', 'partially_dispensed']) && auth()->user()->hasRole(['super_admin', 'branch_admin', 'pharmacist']))
        <form method="POST" action="{{ route('prescriptions.dispense', $order) }}" style="margin-top: 1rem;">
            @csrf
            <button class="primary-button" type="submit">Dispense prescription</button>
        </form>
    @endif
</div>
@endsection

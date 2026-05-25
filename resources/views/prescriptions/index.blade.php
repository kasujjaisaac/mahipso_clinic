@extends('layouts.app')

@section('title', 'Prescriptions')
@section('section', 'Pharmacy')
@section('page_title', 'Prescription orders')

@section('content')
<div class="panel">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Order</th><th>Patient</th><th>Provider</th><th>Status</th><th>Total</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->patient->full_name ?? 'N/A' }}</td>
                        <td>{{ $order->provider->name ?? 'N/A' }}</td>
                        <td><span class="status-pill {{ $order->status }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td>
                        <td>USh {{ number_format($order->items->sum('total_price'), 0) }}</td>
                        <td><a class="ghost-button" href="{{ route('prescriptions.show', $order) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">No prescriptions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">{{ $orders->links() }}</div>
</div>
@endsection

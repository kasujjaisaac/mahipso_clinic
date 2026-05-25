@extends('layouts.app')
@section('title', 'Purchase Orders')
@section('page_title', 'Purchase orders')
@section('topbar_actions')<a class="primary-button" href="{{ route('purchase-orders.create') }}">New order</a>@endsection
@section('content')
<div class="panel"><div class="table-wrap"><table><thead><tr><th>ID</th><th>Supplier</th><th>Branch</th><th>Status</th><th>Total</th><th>Expected</th></tr></thead><tbody>
@forelse($orders as $order)
<tr><td>#{{ $order->id }}</td><td>{{ $order->supplier->name ?? '-' }}</td><td>{{ $order->branch->name ?? '-' }}</td><td>{{ ucfirst($order->status) }}</td><td>USh {{ number_format($order->total_amount, 0) }}</td><td>{{ optional($order->expected_at)->format('Y-m-d') }}</td></tr>
@empty
<tr><td colspan="6" class="empty-state">No purchase orders.</td></tr>
@endforelse
</tbody></table></div>{{ $orders->links() }}</div>
@endsection

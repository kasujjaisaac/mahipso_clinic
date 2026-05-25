@extends('layouts.app')
@section('title', 'Pharmacy Dashboard')
@section('page_title', 'Pharmacy dashboard')
@section('content')
<div class="panel">
    <h2 class="section-title">Pending prescriptions</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Order</th><th>Patient</th><th>Items</th><th>Action</th></tr></thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->patient->full_name ?? 'N/A' }}</td>
                        <td>{{ $order->items->count() }}</td>
                        <td><a class="ghost-button" href="{{ route('prescriptions.show', $order) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No pending prescriptions.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

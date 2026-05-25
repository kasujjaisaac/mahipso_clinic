@extends('layouts.app')
@section('title', 'New Purchase Order')
@section('page_title', 'New purchase order')
@section('content')
<div class="panel"><form method="POST" action="{{ route('purchase-orders.store') }}" class="form-grid">@csrf
<div><label>Supplier</label><select name="supplier_id"><option value="">Select supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach</select></div>
<div><label>Total amount</label><input type="number" name="total_amount" min="0" step="0.01" required></div>
<div><label>Expected date</label><input type="date" name="expected_at"></div>
<div class="field-span-2"><label>Notes</label><textarea name="notes"></textarea></div>
<div><button class="primary-button">Submit order</button></div>
</form></div>
@endsection

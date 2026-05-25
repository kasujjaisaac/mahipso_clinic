@extends('layouts.app')
@section('title', 'New Service')
@section('page_title', 'New service')
@section('content')
<div class="panel"><form method="POST" action="{{ route('service-items.store') }}" class="form-grid">@csrf
<div><label>Branch</label><select name="branch_id"><option value="">Global</option>@foreach($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach</select></div>
<div><label>Name</label><input name="name" required></div>
<div><label>Category</label><select name="category" required><option value="consultation">Consultation</option><option value="laboratory">Laboratory</option><option value="procedure">Procedure</option><option value="other">Other</option></select></div>
<div><label>Price</label><input type="number" name="price" min="0" step="0.01" required></div>
<div><label><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
<div><button class="primary-button">Save service</button></div>
</form></div>
@endsection

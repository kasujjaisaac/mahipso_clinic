@extends('layouts.app')
@section('title', 'New Lab Catalogue Test')
@section('page_title', 'New lab catalogue test')
@section('content')
<div class="panel"><form method="POST" action="{{ route('lab-catalog.store') }}" class="form-grid">@csrf
<div><label>Branch</label><select name="branch_id"><option value="">Global</option>@foreach($branches as $branch)<option value="{{ $branch->id }}">{{ $branch->name }}</option>@endforeach</select></div>
<div><label>Test name</label><input name="test_name" required></div>
<div><label>Sample type</label><input name="sample_type"></div>
<div><label>Unit</label><input name="unit"></div>
<div><label>Reference range</label><input name="reference_range"></div>
<div><label>Price</label><input type="number" name="price" min="0" step="0.01" required></div>
<div><label><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
<div><button class="primary-button">Save test</button></div>
</form></div>
@endsection

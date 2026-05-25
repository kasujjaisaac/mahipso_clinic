@extends('layouts.app')
@section('title', 'Service Catalogue')
@section('page_title', 'Service catalogue')
@section('topbar_actions')<a class="primary-button" href="{{ route('service-items.create') }}">New service</a>@endsection
@section('content')
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Branch</th><th>Status</th></tr></thead><tbody>
@forelse($services as $service)
<tr><td>{{ $service->name }}</td><td>{{ ucfirst($service->category) }}</td><td>USh {{ number_format($service->price, 0) }}</td><td>{{ $service->branch->name ?? 'Global' }}</td><td>{{ $service->is_active ? 'Active' : 'Inactive' }}</td></tr>
@empty
<tr><td colspan="5" class="empty-state">No services configured.</td></tr>
@endforelse
</tbody></table></div>{{ $services->links() }}</div>
@endsection

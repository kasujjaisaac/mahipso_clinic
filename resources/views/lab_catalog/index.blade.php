@extends('layouts.app')
@section('title', 'Lab Catalogue')
@section('page_title', 'Lab catalogue')
@section('topbar_actions')<a class="primary-button" href="{{ route('lab-catalog.create') }}">New lab test</a>@endsection
@section('content')
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Test</th><th>Sample</th><th>Unit</th><th>Reference</th><th>Price</th><th>Branch</th></tr></thead><tbody>
@forelse($tests as $test)
<tr><td>{{ $test->test_name }}</td><td>{{ $test->sample_type }}</td><td>{{ $test->unit }}</td><td>{{ $test->reference_range }}</td><td>USh {{ number_format($test->price, 0) }}</td><td>{{ $test->branch->name ?? 'Global' }}</td></tr>
@empty
<tr><td colspan="6" class="empty-state">No lab tests configured.</td></tr>
@endforelse
</tbody></table></div>{{ $tests->links() }}</div>
@endsection

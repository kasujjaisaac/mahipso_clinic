<div class="table-wrap">
    <table>
        <thead><tr><th>Test</th><th>Patient</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
            @forelse($labTests as $labTest)
                <tr><td>{{ $labTest->test_type }}</td><td>{{ $labTest->patient->full_name ?? 'N/A' }}</td><td>{{ ucfirst(str_replace('_', ' ', $labTest->status)) }}</td><td><a class="ghost-button" href="{{ route('laboratory.edit', $labTest) }}">Open</a></td></tr>
            @empty
                <tr><td colspan="4" class="empty-state">No lab work found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

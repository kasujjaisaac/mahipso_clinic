<div class="table-wrap">
    <table>
        <thead><tr><th>Visit</th><th>Patient</th><th>Stage</th><th>Action</th></tr></thead>
        <tbody>
            @forelse($visits as $visit)
                <tr><td>#{{ $visit->id }}</td><td>{{ $visit->patient->full_name ?? 'N/A' }}</td><td>{{ $visit->workflow_stage_label }}</td><td><a class="ghost-button" href="{{ route('visits.show', $visit) }}">Open</a></td></tr>
            @empty
                <tr><td colspan="4" class="empty-state">No visits waiting.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

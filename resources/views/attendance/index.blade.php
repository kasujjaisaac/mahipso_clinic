@extends('layouts.app')
@section('title', 'Attendance')
@section('page_title', 'Staff attendance')
@section('content')
<div class="panel">
<form method="POST" action="{{ route('attendance.store') }}" class="form-grid">@csrf
<div><label>Staff</label><select name="user_id" required>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
<div><label>Date</label><input type="date" name="work_date" value="{{ date('Y-m-d') }}" required></div>
<div><label>Clock in</label><input type="time" name="clock_in"></div>
<div><label>Clock out</label><input type="time" name="clock_out"></div>
<div><label>Status</label><select name="status"><option value="present">Present</option><option value="late">Late</option><option value="absent">Absent</option><option value="leave">Leave</option></select></div>
<div><button class="primary-button">Save attendance</button></div>
</form>
</div>
<div class="panel"><div class="table-wrap"><table><thead><tr><th>Date</th><th>Staff</th><th>Status</th><th>In</th><th>Out</th></tr></thead><tbody>
@forelse($attendances as $row)
<tr><td>{{ $row->work_date->format('Y-m-d') }}</td><td>{{ $row->user->name ?? '-' }}</td><td>{{ ucfirst($row->status) }}</td><td>{{ $row->clock_in }}</td><td>{{ $row->clock_out }}</td></tr>
@empty
<tr><td colspan="5" class="empty-state">No attendance records.</td></tr>
@endforelse
</tbody></table></div>{{ $attendances->links() }}</div>
@endsection

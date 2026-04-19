@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">
                    <i class="fas fa-history"></i> Comprehensive Audit Logs
                </h1>
                <div class="btn-group" role="group">
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-list"></i> All Logs
                    </a>
                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('audit-logs.index', array_merge(request()->query(), ['export' => 'csv'])) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> Export CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Filters -->
    @if(request()->hasAny(['user_id', 'action_type', 'module', 'start_date', 'end_date', 'login_status', 'ip_address', 'device_type', 'search']))
        <div class="alert alert-info mb-3" role="alert">
            <strong>Active Filters:</strong>
            @if(request()->filled('user_id'))
                <span class="badge bg-primary">User: {{ App\Models\User::find(request('user_id'))?->name }}</span>
            @endif
            @if(request()->filled('action_type'))
                <span class="badge bg-primary">Action: {{ request('action_type') }}</span>
            @endif
            @if(request()->filled('module'))
                <span class="badge bg-primary">Module: {{ request('module') }}</span>
            @endif
            @if(request()->filled('login_status'))
                <span class="badge bg-primary">Status: {{ request('login_status') }}</span>
            @endif
            @if(request()->filled('device_type'))
                <span class="badge bg-primary">Device: {{ request('device_type') }}</span>
            @endif
            @if(request()->filled('search'))
                <span class="badge bg-primary">Search: {{ request('search') }}</span>
            @endif
            <a href="{{ route('audit-logs.index') }}" class="badge bg-danger text-decoration-none">Clear All</a>
        </div>
    @endif

    <!-- Audit Logs Table -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Audit Trail</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Description</th>
                        <th>Browser & Device</th>
                        <th>IP Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i>
                                    {{ $log->created_at->format('d M Y H:i:s') }}
                                </small>
                            </td>
                            <td>
                                @if($log->user)
                                    <span class="badge bg-primary">{{ $log->user->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->action_badge }}">
                                    {{ ucfirst($log->action_type ?? $log->action) }}
                                </span>
                            </td>
                            <td>
                                @if($log->module)
                                    <span class="badge bg-info">{{ $log->module }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ Str::limit($log->description, 80) }}</small>
                                @if($log->session_duration_minutes)
                                    <br><small class="text-success"><i class="fas fa-hourglass-end"></i> {{ $log->session_duration_formatted }}</small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    @if($log->browser)
                                        <strong>{{ $log->browser }}</strong> {{ $log->browser_version }}<br>
                                        {{ $log->operating_system }}<br>
                                        <span class="badge bg-secondary">{{ ucfirst($log->device_type) }}</span>
                                    @else
                                        -
                                    @endif
                                </small>
                            </td>
                            <td>
                                <code>{{ $log->ip_address ?? '-' }}</code>
                            </td>
                            <td>
                                @if($log->login_status)
                                    <span class="badge bg-{{ $log->login_status === 'success' ? 'success' : 'danger' }}">
                                        {{ ucfirst($log->login_status) }}
                                    </span>
                                @elseif($log->status)
                                    <span class="badge bg-{{ $log->status === 'completed' ? 'success' : ($log->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> No audit logs found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="card-footer bg-light">
            {{ $logs->links() }}
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Audit Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('audit-logs.index') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_id" class="form-label">User</label>
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="">All Users</option>
                                @foreach($users as $userId => $userName)
                                    <option value="{{ $userId }}" {{ request('user_id') == $userId ? 'selected' : '' }}>
                                        {{ $userName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="action_type" class="form-label">Action Type</label>
                            <select class="form-select" id="action_type" name="action_type">
                                <option value="">All Actions</option>
                                @foreach($actionTypes as $type)
                                    <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="module" class="form-label">Module</label>
                            <select class="form-select" id="module" name="module">
                                <option value="">All Modules</option>
                                @foreach($modules as $mod)
                                    <option value="{{ $mod }}" {{ request('module') == $mod ? 'selected' : '' }}>
                                        {{ $mod }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="device_type" class="form-label">Device Type</label>
                            <select class="form-select" id="device_type" name="device_type">
                                <option value="">All Devices</option>
                                @foreach($deviceTypes as $device)
                                    <option value="{{ $device }}" {{ request('device_type') == $device ? 'selected' : '' }}>
                                        {{ ucfirst($device) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                   value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                   value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="search" class="form-label">Search Description</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               placeholder="Search in descriptions..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="{{ route('audit-logs.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 4px solid #007bff;
    }
    .border-left-success {
        border-left: 4px solid #28a745;
    }
    .border-left-danger {
        border-left: 4px solid #dc3545;
    }
    .border-left-warning {
        border-left: 4px solid #ffc107;
    }
    .text-primary { color: #007bff; }
    .text-success { color: #28a745; }
    .text-danger { color: #dc3545; }
    .text-warning { color: #ffc107; }
</style>

@endsection

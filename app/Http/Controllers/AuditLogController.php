<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            abort_unless($request->user()->canAccessModule('administration'), 403);

            return $next($request);
        });
    }

    /**
     * Display all audit logs with filtering
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');
        $this->scopeToUserBranch($query, $request);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter by action type
        if ($request->filled('action_type')) {
            $query->byActionType($request->action_type);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->byModule($request->module);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Filter by login status
        if ($request->filled('login_status')) {
            $query->where('login_status', $request->login_status);
        }

        // Filter by IP address
        if ($request->filled('ip_address')) {
            $query->byIpAddress($request->ip_address);
        }

        // Filter by device type
        if ($request->filled('device_type')) {
            $query->byDeviceType($request->device_type);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Get distinct values for filter dropdowns
        $users = User::when(! $request->user()->isSuperAdmin(), fn ($query) => $query->where('branch_id', $request->user()->branch_id))
            ->pluck('name', 'id');

        $metadataQuery = AuditLog::query();
        $this->scopeToUserBranch($metadataQuery, $request);
        $actionTypes = (clone $metadataQuery)->distinct()->pluck('action_type')->filter();
        $modules = (clone $metadataQuery)->distinct()->pluck('module')->filter();
        $deviceTypes = (clone $metadataQuery)->distinct()->pluck('device_type')->filter();
        $browsers = (clone $metadataQuery)->distinct()->pluck('browser')->filter();

        // Get statistics
        $statsQuery = AuditLog::query();
        $this->scopeToUserBranch($statsQuery, $request);
        $totalLogins = (clone $statsQuery)->logins()->count();
        $successfulLogins = (clone $statsQuery)->successfulLogins()->count();
        $failedLogins = (clone $statsQuery)->failedLogins()->count();
        $activeSessions = (clone $statsQuery)->activeSessions()->count();

        $logs = $query->latest('created_at')->paginate(50);

        return view('audit_logs.index', compact(
            'logs',
            'users',
            'actionTypes',
            'modules',
            'deviceTypes',
            'browsers',
            'totalLogins',
            'successfulLogins',
            'failedLogins',
            'activeSessions'
        ));
    }

    /**
     * Show login history for a specific user
     */
    public function userLoginHistory($userId)
    {
        $user = User::findOrFail($userId);
        $this->authorizeUserInBranch($user);

        $loginHistory = AuditLog::userLoginHistory($userId)
            ->limit(50)
            ->get();

        return view('audit_logs.user_login_history', compact('user', 'loginHistory'));
    }

    /**
     * Show active sessions
     */
    public function activeSessions()
    {
        $query = AuditLog::activeSessions();
        $this->scopeToUserBranch($query, request());

        $sessions = $query
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('audit_logs.active_sessions', compact('sessions'));
    }

    /**
     * Show suspicious activities
     */
    public function suspiciousActivities()
    {
        $query = AuditLog::suspiciousActivity();
        $this->scopeToUserBranch($query, request());

        $suspiciousActivities = $query
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('audit_logs.suspicious_activities', compact('suspiciousActivities'));
    }

    /**
     * Show user activity report
     */
    public function userActivityReport($userId)
    {
        $user = User::findOrFail($userId);
        $this->authorizeUserInBranch($user);

        // Get activity breakdown
        $activityByModule = AuditLog::where('user_id', $userId)
            ->select('module', DB::raw('count(*) as count'))
            ->groupBy('module')
            ->get();

        $activityByActionType = AuditLog::where('user_id', $userId)
            ->select('action_type', DB::raw('count(*) as count'))
            ->groupBy('action_type')
            ->get();

        $activityByDate = AuditLog::where('user_id', $userId)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        $totalActions = AuditLog::where('user_id', $userId)->count();
        $loginCount = AuditLog::where('user_id', $userId)->logins()->count();
        $lastLogin = AuditLog::where('user_id', $userId)->logins()->latest('created_at')->first();
        $mostUsedModule = $activityByModule->first();

        return view('audit_logs.user_activity_report', compact(
            'user',
            'activityByModule',
            'activityByActionType',
            'activityByDate',
            'totalActions',
            'loginCount',
            'lastLogin',
            'mostUsedModule'
        ));
    }

    /**
     * Export audit logs as CSV
     */
    public function export(Request $request)
    {
        $query = AuditLog::query();
        $this->scopeToUserBranch($query, $request);

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        if ($request->filled('action_type')) {
            $query->byActionType($request->action_type);
        }

        if ($request->filled('module')) {
            $query->byModule($request->module);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        $logs = $query->latest('created_at')->get();

        $filename = 'audit_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            'ID',
            'User',
            'Action Type',
            'Module',
            'Description',
            'IP Address',
            'Browser',
            'Device Type',
            'Login Status',
            'Session Duration (minutes)',
            'Timestamp',
        ];

        $callback = function() use ($logs, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'N/A',
                    $log->action_type,
                    $log->module,
                    $log->description,
                    $log->ip_address,
                    $log->browser,
                    $log->device_type,
                    $log->login_status,
                    $log->session_duration_minutes,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function scopeToUserBranch($query, Request $request): void
    {
        if ($request->user()->isSuperAdmin()) {
            return;
        }

        $query->where(function ($branchQuery) use ($request) {
            $branchQuery
                ->where('branch_id', $request->user()->branch_id)
                ->orWhereHas('user', fn ($userQuery) => $userQuery->where('branch_id', $request->user()->branch_id));
        });
    }

    private function authorizeUserInBranch(User $user): void
    {
        abort_unless(auth()->user()->isSuperAdmin() || auth()->user()->branch_id === $user->branch_id, 404);
    }
}

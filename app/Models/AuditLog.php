<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'action',
        'action_type',
        'module',
        'description',
        'url',
        'method',
        'ip_address',
        'user_agent',
        'session_id',
        'login_time',
        'logout_time',
        'login_status',
        'session_duration_minutes',
        'browser',
        'browser_version',
        'operating_system',
        'device_type',
        'old_values',
        'new_values',
        'changes_summary',
        'resource_type',
        'resource_id',
        'status',
        'error_message',
    ];

    protected $casts = [
        'login_time' => 'datetime',
        'logout_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Query Scopes for Filtering
     */

    /**
     * Filter logs by user
     */
    public function scopeByUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Filter logs by module
     */
    public function scopeByModule(Builder $query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Filter logs by action type
     */
    public function scopeByActionType(Builder $query, $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Filter login activities
     */
    public function scopeLogins(Builder $query)
    {
        return $query->where('action_type', 'login');
    }

    /**
     * Filter logout activities
     */
    public function scopeLogouts(Builder $query)
    {
        return $query->where('action_type', 'logout');
    }

    /**
     * Filter successful logins
     */
    public function scopeSuccessfulLogins(Builder $query)
    {
        return $query->where('action_type', 'login')->where('login_status', 'success');
    }

    /**
     * Filter failed logins
     */
    public function scopeFailedLogins(Builder $query)
    {
        return $query->where('action_type', 'login')->where('login_status', 'failed');
    }

    /**
     * Filter by date range
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Filter by IP address
     */
    public function scopeByIpAddress(Builder $query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Filter by browser
     */
    public function scopeByBrowser(Builder $query, $browser)
    {
        return $query->where('browser', $browser);
    }

    /**
     * Filter by device type
     */
    public function scopeByDeviceType(Builder $query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Filter by status
     */
    public function scopeByStatus(Builder $query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Filter today's activity
     */
    public function scopeToday(Builder $query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Filter this week's activity
     */
    public function scopeThisWeek(Builder $query)
    {
        return $query->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ]);
    }

    /**
     * Filter this month's activity
     */
    public function scopeThisMonth(Builder $query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    /**
     * Get user login history
     */
    public function scopeUserLoginHistory(Builder $query, $userId)
    {
        return $query->where('user_id', $userId)
            ->where('action_type', 'login')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get active sessions (logged in but not logged out)
     */
    public function scopeActiveSessions(Builder $query)
    {
        return $query->where('action_type', 'login')
            ->where('login_status', 'success')
            ->whereNull('logout_time');
    }

    /**
     * Get suspicious activities (failed logins from same IP)
     */
    public function scopeSuspiciousActivity(Builder $query)
    {
        return $query->where('login_status', 'failed')
            ->where('created_at', '>', now()->subHours(24));
    }

    /**
     * Filter by resource type and ID
     */
    public function scopeByResource(Builder $query, $resourceType, $resourceId)
    {
        return $query->where('resource_type', $resourceType)
            ->where('resource_id', $resourceId);
    }

    /**
     * Accessors for formatted display
     */
    public function getSessionDurationFormattedAttribute()
    {
        if (!$this->session_duration_minutes) {
            return '-';
        }

        $hours = intdiv($this->session_duration_minutes, 60);
        $minutes = $this->session_duration_minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown User';
    }

    public function getFormattedTimestampAttribute()
    {
        return $this->created_at->format('d M Y H:i:s');
    }

    public function getDeviceInfoAttribute()
    {
        return "{$this->operating_system} - {$this->browser} {$this->browser_version}";
    }

    public function getActionBadgeAttribute()
    {
        $badges = [
            'login' => 'success',
            'logout' => 'info',
            'access' => 'primary',
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            'view' => 'secondary',
        ];

        return $badges[$this->action_type] ?? 'secondary';
    }
}

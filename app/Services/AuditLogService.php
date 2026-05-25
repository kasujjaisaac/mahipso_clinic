<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Helpers\BrowserHelper;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class AuditLogService
{
    private static function currentBranchId(): ?int
    {
        return auth()->check() ? auth()->user()->branch_id : null;
    }

    /**
     * Get user agent information
     */
    private static function parseUserAgent(string $userAgent): array
    {
        return BrowserHelper::parseUserAgent($userAgent);
    }

    private static function parseLoginTime(mixed $loginTime): ?Carbon
    {
        if ($loginTime instanceof Carbon) {
            return $loginTime;
        }

        if ($loginTime instanceof DateTimeInterface) {
            return Carbon::instance($loginTime);
        }

        if (is_numeric($loginTime)) {
            return Carbon::createFromTimestamp((int) $loginTime);
        }

        if (is_string($loginTime) && trim($loginTime) !== '') {
            try {
                return Carbon::parse($loginTime);
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    /**
     * Log general access to modules
     */
    public static function logAccess(Request $request, string $action, string $module = null, string $description = null)
    {
        $userAgentInfo = self::parseUserAgent($request->userAgent());

        AuditLog::create([
            'user_id' => auth()->id(),
            'branch_id' => self::currentBranchId(),
            'action' => $action,
            'action_type' => 'access',
            'module' => $module,
            'description' => $description,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
            'browser' => $userAgentInfo['browser'],
            'browser_version' => $userAgentInfo['browser_version'],
            'operating_system' => $userAgentInfo['operating_system'],
            'device_type' => $userAgentInfo['device_type'],
            'status' => 'completed',
        ]);
    }

    /**
     * Log user login with comprehensive tracking
     */
    public static function logLogin(Request $request, bool $success = true, string $reason = null)
    {
        $userAgentInfo = self::parseUserAgent($request->userAgent());
        $userId = $success ? auth()->id() : null;
        $sessionId = $request->session()->getId();

        // Store login time in cache for later use during logout
        if ($success && auth()->check()) {
            Cache::put("login_time_{$sessionId}", now()->toIso8601String(), now()->addHours(24));
        }

        AuditLog::create([
            'user_id' => $userId,
            'branch_id' => $success && auth()->check() ? auth()->user()->branch_id : null,
            'action' => 'login',
            'action_type' => 'login',
            'module' => 'authentication',
            'description' => $success 
                ? 'User successfully logged in' 
                : 'Login attempt failed - ' . ($reason ?? 'Invalid credentials'),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $sessionId,
            'login_time' => now(),
            'login_status' => $success ? 'success' : 'failed',
            'browser' => $userAgentInfo['browser'],
            'browser_version' => $userAgentInfo['browser_version'],
            'operating_system' => $userAgentInfo['operating_system'],
            'device_type' => $userAgentInfo['device_type'],
            'status' => $success ? 'completed' : 'failed',
        ]);
    }

    /**
     * Log user logout with session duration
     */
    public static function logLogout(Request $request)
    {
        $userAgentInfo = self::parseUserAgent($request->userAgent());
        $sessionId = $request->session()->getId();
        $userId = auth()->id();

        // Calculate session duration
        $loginTimeCacheKey = "login_time_{$sessionId}";
        $loginTime = self::parseLoginTime(Cache::get($loginTimeCacheKey));
        $sessionDurationMinutes = null;

        if ($loginTime) {
            $sessionDurationMinutes = $loginTime->diffInMinutes(now());
            Cache::forget($loginTimeCacheKey);
        } else {
            Cache::forget($loginTimeCacheKey);

            // If login time not in cache, try to find the most recent login
            $lastLogin = AuditLog::where('user_id', $userId)
                ->where('action_type', 'login')
                ->where('login_status', 'success')
                ->latest('login_time')
                ->first();

            if ($lastLogin && $lastLogin->login_time) {
                $sessionDurationMinutes = $lastLogin->login_time->diffInMinutes(now());
            }
        }

        AuditLog::create([
            'user_id' => $userId,
            'branch_id' => self::currentBranchId(),
            'action' => 'logout',
            'action_type' => 'logout',
            'module' => 'authentication',
            'description' => 'User logged out',
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $sessionId,
            'logout_time' => now(),
            'login_status' => 'success',
            'session_duration_minutes' => $sessionDurationMinutes,
            'browser' => $userAgentInfo['browser'],
            'browser_version' => $userAgentInfo['browser_version'],
            'operating_system' => $userAgentInfo['operating_system'],
            'device_type' => $userAgentInfo['device_type'],
            'status' => 'completed',
        ]);
    }

    /**
     * Log data modification (create, update, delete)
     */
    public static function logDataChange(
        Request $request,
        string $actionType,
        string $resourceType,
        int $resourceId,
        array $oldValues = [],
        array $newValues = [],
        string $description = null
    ) {
        $userAgentInfo = self::parseUserAgent($request->userAgent());

        // Generate change summary
        $changesSummary = self::generateChangeSummary($oldValues, $newValues);

        AuditLog::create([
            'user_id' => auth()->id(),
            'branch_id' => self::currentBranchId(),
            'action' => $actionType,
            'action_type' => $actionType,
            'module' => null,
            'description' => $description ?? "User {$actionType}d {$resourceType} (ID: {$resourceId})",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
            'browser' => $userAgentInfo['browser'],
            'browser_version' => $userAgentInfo['browser_version'],
            'operating_system' => $userAgentInfo['operating_system'],
            'device_type' => $userAgentInfo['device_type'],
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
            'changes_summary' => $changesSummary,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'status' => 'completed',
        ]);
    }

    /**
     * Generate a human-readable summary of changes
     */
    private static function generateChangeSummary(array $oldValues, array $newValues): ?string
    {
        if (empty($oldValues) && empty($newValues)) {
            return null;
        }

        $changes = [];

        foreach ($newValues as $key => $newValue) {
            $oldValue = $oldValues[$key] ?? null;

            if ($oldValue !== $newValue) {
                $oldValueStr = is_array($oldValue) ? json_encode($oldValue) : $oldValue;
                $newValueStr = is_array($newValue) ? json_encode($newValue) : $newValue;
                $changes[] = "{$key}: '{$oldValueStr}' → '{$newValueStr}'";
            }
        }

        return !empty($changes) ? implode(', ', $changes) : null;
    }

    /**
     * Log failed login attempt with IP tracking
     */
    public static function logFailedLoginAttempt(Request $request, string $email)
    {
        $ipAddress = $request->ip();
        $failedAttempts = (int) Cache::get("failed_login_attempts_{$ipAddress}", 0);
        $attemptNumber = $failedAttempts + 1;

        AuditLog::create([
            'user_id' => null,
            'branch_id' => null,
            'action' => 'failed_login_attempt',
            'action_type' => 'login',
            'module' => 'authentication',
            'description' => "Failed login attempt for email: {$email}. Attempt #{$attemptNumber}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $ipAddress,
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
            'login_status' => 'failed',
            'status' => 'failed',
        ]);

        // Track failed attempts for rate limiting/security
        Cache::increment("failed_login_attempts_{$ipAddress}");
        Cache::put("failed_login_attempts_{$ipAddress}", $attemptNumber, now()->addHours(1));
    }

    /**
     * Log suspicious activity (multiple failed logins, etc.)
     */
    public static function logSuspiciousActivity(Request $request, string $reason)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'branch_id' => self::currentBranchId(),
            'action' => 'suspicious_activity',
            'action_type' => 'access',
            'module' => 'security',
            'description' => "Suspicious activity detected: {$reason}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
            'status' => 'completed',
        ]);
    }

    /**
     * Log admin actions
     */
    public static function logAdminAction(
        Request $request,
        string $actionType,
        string $targetUser,
        string $description
    ) {
        $userAgentInfo = self::parseUserAgent($request->userAgent());

        AuditLog::create([
            'user_id' => auth()->id(),
            'branch_id' => self::currentBranchId(),
            'action' => $actionType,
            'action_type' => 'update',
            'module' => 'admin',
            'description' => "Admin action: {$description} on user {$targetUser}",
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
            'browser' => $userAgentInfo['browser'],
            'browser_version' => $userAgentInfo['browser_version'],
            'operating_system' => $userAgentInfo['operating_system'],
            'device_type' => $userAgentInfo['device_type'],
            'status' => 'completed',
        ]);
    }

    /**
     * Get user login sessions summary
     */
    public static function getUserLoginSessions($userId, $limit = 10)
    {
        return AuditLog::where('user_id', $userId)
            ->where('action_type', 'login')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get currently active sessions
     */
    public static function getActiveSessions()
    {
        return AuditLog::activeSessions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get suspicious activities
     */
    public static function getSuspiciousActivities()
    {
        return AuditLog::suspiciousActivity()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

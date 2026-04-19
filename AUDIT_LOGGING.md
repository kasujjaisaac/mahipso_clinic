# Comprehensive Audit Logging System

This document describes the comprehensive audit logging system implemented in the clinic management system.

## Overview

The audit logging system is designed to track and record all user activities within the system, with a focus on:

- **User Logins/Logouts**: Track when users log in and out, including session duration
- **Module Access**: Record which modules users access and when
- **Data Changes**: Log all create, update, and delete operations with before/after values
- **Device & Browser Information**: Capture browser, OS, and device type information
- **Suspicious Activities**: Flag and monitor suspicious login attempts and activities
- **Security Intelligence**: IP address tracking, failed login attempts, and more

## Database Schema

### `audit_logs` Table Structure

```
id                      - Unique identifier
user_id                 - User who performed the action
action                  - Action performed
action_type             - Type of action (login, logout, access, create, update, delete, view)
module                  - Module accessed
description             - Human-readable description
url                     - Full URL of the request
method                  - HTTP method (GET, POST, PUT, DELETE)
ip_address              - User's IP address
user_agent              - Browser user agent string
session_id              - Session identifier
login_time              - Time user logged in
logout_time             - Time user logged out
login_status            - success or failed
session_duration_minutes - How long the session lasted
browser                 - Browser name
browser_version         - Browser version
operating_system        - Operating system
device_type             - desktop, tablet, or mobile
old_values              - JSON of previous values (for updates)
new_values              - JSON of new values (for updates)
changes_summary         - Human-readable summary of changes
resource_type           - Type of resource affected (e.g., Patient, Appointment)
resource_id             - ID of affected resource
status                  - completed, pending, or failed
error_message           - Error details if failed
created_at              - Timestamp
updated_at              - Timestamp
```

## Key Features

### 1. Login/Logout Tracking

- **Success Tracking**: Records successful logins with device and browser information
- **Failed Attempt Tracking**: Logs failed login attempts with email address
- **Session Duration**: Calculates and stores how long each session lasted
- **Device Identification**: Captures browser, OS, and device type

Example login entry:
```
User: John Doe
Action: login
Status: success
Browser: Chrome 120
OS: macOS
Device: desktop
Session Duration: 2h 45m
Timestamp: 2026-04-19 10:30:15
```

### 2. Module Access Tracking

- **Module Views**: Logs when users access different modules
- **Action Type**: Distinguishes between view, create, update, delete operations
- **Breadcrumb Navigation**: Shows which specific actions were performed

Example module access entry:
```
User: Sarah Smith
Action: view
Module: Patients
Description: User viewed list in Patients module
Timestamp: 2026-04-19 11:15:22
```

### 3. Data Change Tracking

- **Before/After Values**: Stores JSON of changed fields
- **Change Summary**: Human-readable description of what changed
- **Resource Tracking**: Links changes to specific resources

Example data change entry:
```
User: Mike Johnson
Action: update
Module: Patients
Resource: Patient ID 123
Changes: name: 'John Doe' → 'John David Doe', phone: '555-1234' → '555-5678'
Status: completed
Timestamp: 2026-04-19 14:20:45
```

### 4. Security Monitoring

- **Failed Login Attempts**: Tracks multiple failed login attempts from same IP
- **Suspicious Activity Detection**: Flags unusual patterns
- **IP Address Tracking**: Records all IP addresses used by users
- **Device Changes**: Monitors when users access from new devices

## Using the Audit Log Service

### Basic Access Logging

```php
use App\Services\AuditLogService;

// Log module access
AuditLogService::logAccess($request, 'view', 'patients', 'User viewed patient list');
```

### Login/Logout Logging

```php
// Successful login (automatically called in AuthController)
AuditLogService::logLogin($request, true);

// Failed login attempt
AuditLogService::logFailedLoginAttempt($request, $email);

// Logout (automatically called in AuthController)
AuditLogService::logLogout($request);
```

### Data Change Logging

```php
// Log when updating a resource
$oldValues = [
    'name' => 'Old Name',
    'email' => 'old@example.com'
];

$newValues = [
    'name' => 'New Name',
    'email' => 'new@example.com'
];

AuditLogService::logDataChange(
    $request,
    'update',
    'Patient',
    $patientId,
    $oldValues,
    $newValues,
    'Patient information updated'
);
```

### Suspicious Activity Logging

```php
// Log suspicious activities
AuditLogService::logSuspiciousActivity(
    $request,
    'Multiple failed login attempts from same IP'
);
```

### Admin Actions Logging

```php
// Log when admin performs actions
AuditLogService::logAdminAction(
    $request,
    'password_reset',
    $targetUser->name,
    'Admin reset password for user'
);
```

## Using Query Scopes

The AuditLog model includes many convenient query scopes:

```php
use App\Models\AuditLog;

// Get logins for a specific user
$logins = AuditLog::byUser($userId)->logins()->get();

// Get successful logins
$successfulLogins = AuditLog::successfulLogins()->get();

// Get failed logins
$failedLogins = AuditLog::failedLogins()->get();

// Get active sessions (logged in but not logged out)
$activeSessions = AuditLog::activeSessions()->get();

// Get suspicious activities
$suspicious = AuditLog::suspiciousActivity()->get();

// Get activities in date range
$logs = AuditLog::dateRange($startDate, $endDate)->get();

// Get activities by module
$patientLogs = AuditLog::byModule('patients')->get();

// Get activities by action type
$createActions = AuditLog::byActionType('create')->get();

// Get activities by IP address
$ipLogs = AuditLog::byIpAddress('192.168.1.1')->get();

// Get activities by device type
$mobileLogs = AuditLog::byDeviceType('mobile')->get();

// Get activity history for today
$todayLogs = AuditLog::today()->get();

// Get activity history for this week
$weekLogs = AuditLog::thisWeek()->get();

// Get activity history for this month
$monthLogs = AuditLog::thisMonth()->get();

// Get user login history
$history = AuditLog::userLoginHistory($userId)->get();

// Get activities by resource
$patientActivities = AuditLog::byResource('Patient', $patientId)->get();
```

## Audit Logs Interface

Access the comprehensive audit logs at: `/audit-logs`

### Features:

1. **Statistics Dashboard**: Shows total logins, successful/failed logins, active sessions
2. **Filtering**: Filter by user, action type, module, date range, device type, etc.
3. **Search**: Search within log descriptions
4. **Export**: Export logs to CSV for external analysis
5. **Pagination**: Navigate through large datasets efficiently

### Routes:

- `GET /audit-logs` - View all audit logs with filtering
- `GET /audit-logs/user/{user}/login-history` - View login history for specific user
- `GET /audit-logs/active-sessions` - View currently active sessions
- `GET /audit-logs/suspicious` - View suspicious activities
- `GET /audit-logs/user/{user}/report` - View activity report for specific user
- `GET /audit-logs/export` - Export logs as CSV

## Middleware Integration

The `AuditLogMiddleware` automatically logs all authenticated user requests. It:

- Logs all module accesses (GET, POST, PUT, DELETE)
- Extracts module information from route names
- Tracks HTTP method and translates to action type
- Captures browser and device information
- Skips logging for certain routes (login, csrf-token, etc.)

The middleware is applied to all authenticated routes.

## Accessors for Display

The AuditLog model includes helpful accessors:

```php
$log->session_duration_formatted  // "2h 45m" or "30m"
$log->user_name                   // User name or "Unknown User"
$log->formatted_timestamp         // "19 Apr 2026 10:30:15"
$log->device_info                 // "macOS - Chrome 120"
$log->action_badge                // CSS badge class (success, danger, etc.)
```

## Best Practices

1. **Be Specific**: Use descriptive action descriptions
2. **Track Changes**: Always log before/after values for data modifications
3. **Include Context**: Add resource types and IDs when applicable
4. **Regular Review**: Periodically review audit logs for security issues
5. **Archive**: Consider archiving old logs periodically
6. **Performance**: Use indexes when filtering large datasets

## Security Considerations

1. **Access Control**: Restrict audit log access to authorized personnel only
2. **Data Retention**: Establish retention policies for old logs
3. **Encryption**: Consider encrypting sensitive data in logs if needed
4. **Monitoring**: Set up alerts for suspicious activities
5. **Audit Trail**: Keep audit logs in append-only mode when possible

## Performance Optimization

The audit logs table includes indexes on:

- `user_id`
- `module`
- `action_type`
- `created_at`
- Composite: `(user_id, created_at)`
- Composite: `(module, action_type, created_at)`

These indexes help with common filtering operations.

## Troubleshooting

### Logs not being recorded
- Ensure middleware is registered in `app/Http/Kernel.php`
- Check that users are authenticated
- Verify the routes are within the `auth` middleware group

### Performance issues
- Check database indexes are created
- Consider archiving old logs
- Use date filtering to limit result sets

### Missing device information
- Ensure `jenssegers/agent` package is installed: `composer require jenssegers/agent`
- Verify user agent is being passed correctly

## Future Enhancements

Possible future improvements:

- Real-time alerts for suspicious activities
- Advanced analytics and dashboards
- Machine learning for anomaly detection
- Blockchain-based immutable logs
- Integration with external security systems
- Advanced reporting and visualization tools

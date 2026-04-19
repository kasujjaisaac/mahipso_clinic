<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuditLogService;

class AuditLogMiddleware
{
    /**
     * Routes to skip audit logging
     */
    private $skipRoutes = [
        'login',
        'auth.verify',
        'health',
        'csrf-token',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (auth()->check()) {
            $routeName = $request->route() ? $request->route()->getName() : null;

            // Skip logging for certain routes
            if ($this->shouldSkip($routeName)) {
                return $response;
            }

            $module = $this->determineModule($routeName);

            if ($module) {
                // Log all actions: GET (view), POST (create), PUT/PATCH (update), DELETE
                $action = $this->getActionType($request->method());
                $description = $this->getActionDescription($action, $module, $routeName);

                AuditLogService::logAccess(
                    $request,
                    $action,
                    $module,
                    $description
                );
            }
        }

        return $response;
    }

    /**
     * Check if route should be skipped from audit logging
     */
    private function shouldSkip(?string $routeName): bool
    {
        if (!$routeName) {
            return false;
        }

        foreach ($this->skipRoutes as $skipRoute) {
            if (str_contains($routeName, $skipRoute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the action type based on HTTP method
     */
    private function getActionType(string $method): string
    {
        return match ($method) {
            'GET' => 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'access',
        };
    }

    /**
     * Get a human-readable description of the action
     */
    private function getActionDescription(string $action, string $module, ?string $routeName): string
    {
        $actionLabels = [
            'view' => 'Viewed',
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'access' => 'Accessed',
        ];

        $actionLabel = $actionLabels[$action] ?? 'Accessed';

        // Extract specific action from route name
        if ($routeName && str_contains($routeName, '.')) {
            $parts = explode('.', $routeName);
            $specificAction = end($parts);

            $actionMap = [
                'index' => 'viewed list',
                'show' => 'viewed details',
                'create' => 'opened create form',
                'store' => 'created record',
                'edit' => 'opened edit form',
                'update' => 'updated record',
                'destroy' => 'deleted record',
                'dashboard' => 'viewed dashboard',
            ];

            if (isset($actionMap[$specificAction])) {
                return "User {$actionMap[$specificAction]} in {$module} module";
            }
        }

        return "User {$actionLabel} {$module} module";
    }

    /**
     * Determine module from route name
     */
    private function determineModule(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }

        $prefixes = [
            'admin.dashboard' => 'Dashboard',
            'branches.' => 'Branches',
            'appointments.' => 'Appointments',
            'patients.' => 'Patients',
            'messages.' => 'Messages',
            'visits.' => 'Visits',
            'medical-records.' => 'Medical Records',
            'hiv-records.' => 'HIV Records',
            'laboratory.' => 'Laboratory',
            'pharmacies.' => 'Pharmacy',
            'inventory.' => 'Inventory',
            'billing.' => 'Billing',
            'expenses.' => 'Expenses',
            'financial.' => 'Financial',
            'reporting.' => 'Reporting',
            'notifications.' => 'Notifications',
            'employees.' => 'Employees',
            'contracts.' => 'Contracts',
            'departments.' => 'Departments',
            'leaves.' => 'Leaves',
            'documents.' => 'Documents',
            'users.' => 'Users',
            'roles.' => 'Roles',
            'audit-logs.' => 'Audit Logs',
            'partners.' => 'Partners',
            'emergencies.' => 'Emergencies',
            'vitals.' => 'Vital Signs',
        ];

        foreach ($prefixes as $prefix => $module) {
            if (str_starts_with($routeName, $prefix)) {
                return $module;
            }
        }

        return null;
    }
}

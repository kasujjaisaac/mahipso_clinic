
<?php

use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->isSuperAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('branches.cards');
});

Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// branches.cards route for dashboard/clinic cards (must be outside auth group for tests)
Route::get('branches/cards', [BranchController::class, 'cards'])->name('branches.cards');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('admin/dashboard', [BranchController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('branches/{branch}/dashboard', [BranchController::class, 'dashboard'])->name('branches.dashboard');
    Route::resource('branches', BranchController::class);

    // Main Modules
    Route::get('appointments/availability', [App\Http\Controllers\AppointmentController::class, 'availability'])->name('appointments.availability');
    Route::resource('appointments', App\Http\Controllers\AppointmentController::class);
    Route::resource('patients', App\Http\Controllers\PatientController::class);
    Route::resource('messages', App\Http\Controllers\MessageController::class);

    // Clinical
    Route::resource('visits', App\Http\Controllers\VisitController::class);
    Route::resource('medical-records', App\Http\Controllers\MedicalRecordController::class);
    Route::resource('hiv-records', App\Http\Controllers\HivRecordController::class);
    Route::resource('laboratory', App\Http\Controllers\LaboratoryController::class);

    // Pharmacy & Inventory
    Route::resource('pharmacies', App\Http\Controllers\PharmacyController::class);
    Route::get('pharmacies/{pharmacy}/analytics', [App\Http\Controllers\PharmacyAnalyticsController::class, 'dashboard'])->name('pharmacies.analytics');
    Route::get('pharmacies/{pharmacy}/reports/low-stock', [App\Http\Controllers\PharmacyAnalyticsController::class, 'lowStockReport'])->name('pharmacies.reports.low-stock');
    Route::get('pharmacies/{pharmacy}/reports/expiry', [App\Http\Controllers\PharmacyAnalyticsController::class, 'expiryReport'])->name('pharmacies.reports.expiry');
    Route::get('pharmacies/{pharmacy}/reports/revenue', [App\Http\Controllers\PharmacyAnalyticsController::class, 'revenueReport'])->name('pharmacies.reports.revenue');
    Route::resource('pharmacies.categories', App\Http\Controllers\ProductCategoryController::class);
    Route::resource('pharmacies.products', App\Http\Controllers\ProductController::class);
    Route::post('pharmacies/{pharmacy}/products/bulk-import', [App\Http\Controllers\ProductController::class, 'bulkImport'])->name('pharmacies.products.bulkImport');
    Route::get('pharmacies/{pharmacy}/products/bulk-export', [App\Http\Controllers\ProductController::class, 'bulkExport'])->name('pharmacies.products.bulkExport');
    Route::resource('pharmacies.sales', App\Http\Controllers\SaleController::class);
    Route::post('pharmacies/{pharmacy}/sales/{sale}/void', [App\Http\Controllers\SaleController::class, 'void'])->name('pharmacies.sales.void');
    Route::post('pharmacies/{pharmacy}/sales/{sale}/refund', [App\Http\Controllers\SaleController::class, 'refund'])->name('pharmacies.sales.refund');
    Route::get('pharmacies/{pharmacy}/sales/{sale}/print-receipt', [App\Http\Controllers\SaleController::class, 'printReceipt'])->name('pharmacies.sales.printReceipt');
    Route::resource('inventory', App\Http\Controllers\InventoryController::class);

    // Billing
    Route::resource('billing', App\Http\Controllers\BillingController::class);
    Route::resource('expenses', App\Http\Controllers\ExpenseController::class);

    // Financial Module
    Route::get('financial', [App\Http\Controllers\FinancialController::class, 'index'])->name('financial.index');
    Route::get('financial/income', [App\Http\Controllers\FinancialController::class, 'income'])->name('financial.income');
    Route::get('financial/expenditure', [App\Http\Controllers\FinancialController::class, 'expenditure'])->name('financial.expenditure');

    // Analytics & Reports
    Route::get('reporting', [App\Http\Controllers\ReportingController::class, 'index'])->name('reporting.index');

    // Notifications
    Route::resource('notifications', App\Http\Controllers\NotificationController::class)->only(['index', 'show', 'destroy', 'create', 'store']);

    // HR Management
    Route::resource('employees', App\Http\Controllers\EmployeeController::class);
    Route::resource('contracts', App\Http\Controllers\ContractController::class);
    Route::resource('departments', App\Http\Controllers\DepartmentController::class);
    Route::resource('leaves', App\Http\Controllers\LeaveController::class);
    Route::resource('documents', App\Http\Controllers\DocumentController::class);

    // Admin & Security
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::get('audit-logs', [App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/user/{user}/login-history', [App\Http\Controllers\AuditLogController::class, 'userLoginHistory'])->name('audit-logs.user-login-history');
    Route::get('audit-logs/active-sessions', [App\Http\Controllers\AuditLogController::class, 'activeSessions'])->name('audit-logs.active-sessions');
    Route::get('audit-logs/suspicious', [App\Http\Controllers\AuditLogController::class, 'suspiciousActivities'])->name('audit-logs.suspicious');
    Route::get('audit-logs/user/{user}/report', [App\Http\Controllers\AuditLogController::class, 'userActivityReport'])->name('audit-logs.user-activity-report');
    Route::get('audit-logs/export', [App\Http\Controllers\AuditLogController::class, 'export'])->name('audit-logs.export');

    // Other
    Route::resource('partners', App\Http\Controllers\PartnerController::class);
    Route::resource('emergencies', App\Http\Controllers\EmergencyController::class);

    // Vital signs (triage) workflow
    Route::get('visits/{visit}/vitals/create', [App\Http\Controllers\VitalSignsController::class, 'create'])->name('vitals.create');
    Route::post('visits/{visit}/vitals', [App\Http\Controllers\VitalSignsController::class, 'store'])->name('vitals.store');
});

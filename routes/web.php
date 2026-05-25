
<?php

use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->isSuperAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('dashboard.role');
});

Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// branches.cards route for dashboard/clinic cards (must be outside auth group for tests)
Route::get('branches/cards', [BranchController::class, 'cards'])->name('branches.cards');

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('dashboard', App\Http\Controllers\StaffPortalController::class)->name('dashboard.role');
    Route::get('staff/dashboard', App\Http\Controllers\StaffPortalController::class)->name('staff.dashboard');
    Route::get('admin/dashboard', [BranchController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('branches/{branch}/dashboard', [BranchController::class, 'dashboard'])->name('branches.dashboard');
    Route::resource('branches', BranchController::class);

    // Account profile
    Route::get('profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Main Modules
    Route::get('appointments/availability', [App\Http\Controllers\AppointmentController::class, 'availability'])->name('appointments.availability');
    Route::resource('appointments', App\Http\Controllers\AppointmentController::class);
    Route::resource('patients', App\Http\Controllers\PatientController::class);
    Route::post('patients/{patient}/allergies', [App\Http\Controllers\PatientAllergyController::class, 'store'])->name('patients.allergies.store');
    Route::delete('patient-allergies/{allergy}', [App\Http\Controllers\PatientAllergyController::class, 'destroy'])->name('patients.allergies.destroy');
    Route::resource('messages', App\Http\Controllers\MessageController::class);

    // Clinical
    Route::get('clinic-queue', [App\Http\Controllers\ClinicQueueController::class, 'index'])->name('clinic-queue.index');
    Route::patch('visits/{visit}/workflow', [App\Http\Controllers\ClinicQueueController::class, 'update'])->name('visits.workflow.update');
    Route::resource('visits', App\Http\Controllers\VisitController::class);
    Route::resource('wards', App\Http\Controllers\WardController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('wards/{ward}/beds', [App\Http\Controllers\BedController::class, 'store'])->name('wards.beds.store');
    Route::patch('beds/{bed}', [App\Http\Controllers\BedController::class, 'update'])->name('beds.update');
    Route::resource('admissions', App\Http\Controllers\AdmissionController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('admissions/{admission}/notes', [App\Http\Controllers\AdmissionController::class, 'addNote'])->name('admissions.notes.store');
    Route::post('admissions/{admission}/vitals', [App\Http\Controllers\AdmissionController::class, 'addVital'])->name('admissions.vitals.store');
    Route::post('admissions/{admission}/medications', [App\Http\Controllers\AdmissionController::class, 'addMedication'])->name('admissions.medications.store');
    Route::post('admissions/{admission}/medication-administrations', [App\Http\Controllers\AdmissionController::class, 'administerMedication'])->name('admissions.medication-administrations.store');
    Route::post('admissions/{admission}/transfer', [App\Http\Controllers\AdmissionController::class, 'transfer'])->name('admissions.transfer');
    Route::post('admissions/{admission}/ready-for-discharge', [App\Http\Controllers\AdmissionController::class, 'markReady'])->name('admissions.ready');
    Route::post('admissions/{admission}/clearance', [App\Http\Controllers\AdmissionController::class, 'updateClearance'])->name('admissions.clearance');
    Route::post('admissions/{admission}/discharge', [App\Http\Controllers\AdmissionController::class, 'discharge'])->name('admissions.discharge');
    Route::resource('medical-records', App\Http\Controllers\MedicalRecordController::class);
    Route::resource('hiv-records', App\Http\Controllers\HivRecordController::class);
    Route::resource('laboratory', App\Http\Controllers\LaboratoryController::class);
    Route::resource('prescriptions', App\Http\Controllers\PrescriptionOrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('prescriptions/{prescription}/dispense', [App\Http\Controllers\PrescriptionOrderController::class, 'dispense'])->name('prescriptions.dispense');

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
    Route::resource('purchase-orders', App\Http\Controllers\PurchaseOrderController::class)->only(['index', 'create', 'store']);

    // Billing
    Route::resource('billing', App\Http\Controllers\BillingController::class);
    Route::resource('service-items', App\Http\Controllers\ServiceItemController::class)->only(['index', 'create', 'store']);
    Route::resource('lab-catalog', App\Http\Controllers\LabCatalogController::class)->only(['index', 'create', 'store']);
    Route::post('visits/{visit}/billing/generate', [App\Http\Controllers\BillingController::class, 'generateFromVisit'])->name('visits.billing.generate');
    Route::post('billing/{bill}/payments', [App\Http\Controllers\PaymentTransactionController::class, 'store'])->name('billing.payments.store');
    Route::get('payments/{payment}/receipt', [App\Http\Controllers\PaymentTransactionController::class, 'receipt'])->name('payments.receipt');
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
    Route::resource('appraisals', App\Http\Controllers\StaffAppraisalController::class);
    Route::resource('payroll', App\Http\Controllers\PayrollController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('payroll/{payroll}/items', [App\Http\Controllers\PayrollController::class, 'updateItem'])->name('payroll.items.update');
    Route::post('payroll/{payroll}/approve', [App\Http\Controllers\PayrollController::class, 'approve'])->name('payroll.approve');
    Route::post('payroll/{payroll}/paid', [App\Http\Controllers\PayrollController::class, 'markPaid'])->name('payroll.paid');
    Route::resource('departments', App\Http\Controllers\DepartmentController::class);
    Route::resource('leaves', App\Http\Controllers\LeaveController::class);
    Route::get('attendance', [App\Http\Controllers\StaffAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('attendance', [App\Http\Controllers\StaffAttendanceController::class, 'store'])->name('attendance.store');
    Route::get('my-requisitions', [App\Http\Controllers\RequisitionController::class, 'mine'])->name('requisitions.mine');
    Route::get('requisitions/{requisition}/print', [App\Http\Controllers\RequisitionController::class, 'print'])->name('requisitions.print');
    Route::resource('requisitions', App\Http\Controllers\RequisitionController::class)->except(['destroy']);
    Route::post('requisitions/{requisition}/supervisor-review', [App\Http\Controllers\RequisitionController::class, 'supervisorReview'])->name('requisitions.supervisor-review');
    Route::post('requisitions/{requisition}/finance-review', [App\Http\Controllers\RequisitionController::class, 'financeReview'])->name('requisitions.finance-review');
    Route::get('my-timesheets', [App\Http\Controllers\MonthlyTimesheetController::class, 'mine'])->name('timesheets.mine');
    Route::get('timesheets/{timesheet}/print', [App\Http\Controllers\MonthlyTimesheetController::class, 'print'])->name('timesheets.print');
    Route::resource('timesheets', App\Http\Controllers\MonthlyTimesheetController::class)->except(['destroy'])->parameters(['timesheets' => 'timesheet']);
    Route::post('timesheets/{timesheet}/supervisor-review', [App\Http\Controllers\MonthlyTimesheetController::class, 'supervisorReview'])->name('timesheets.supervisor-review');
    Route::post('timesheets/{timesheet}/hr-receive', [App\Http\Controllers\MonthlyTimesheetController::class, 'hrReceive'])->name('timesheets.hr-receive');
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

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Bed;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\HivRecord;
use App\Models\Inventory;
use App\Models\LabTest;
use App\Models\MedicalRecord;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Pharmacy;
use App\Models\PayrollRun;
use App\Models\Product;
use App\Models\PrescriptionOrder;
use App\Models\PurchaseOrder;
use App\Models\Requisition;
use App\Models\Role;
use App\Models\Sale;
use App\Models\StaffAppraisal;
use App\Models\StaffAttendance;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BranchController extends Controller
{
    use ResolvesBranchContext;

    public function __construct()
    {
        $this->middleware('auth')->only([
            'adminDashboard',
            'cards',
            'create',
            'dashboard',
            'destroy',
            'edit',
            'index',
            'show',
            'store',
            'update',
        ]);

        $this->middleware(function ($request, $next) {
            abort_unless($this->currentUser()->isSuperAdmin(), 403);

            return $next($request);
        })->only([
            'adminDashboard',
            'create',
            'destroy',
            'edit',
            'index',
            'show',
            'store',
            'update',
        ]);
    }

    public function index()
    {
        $branches = Branch::active()->orderBy('name')->paginate(15);
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'status' => 'required|in:active,inactive',
        ]);

        Branch::create($request->only(['name', 'code', 'address', 'city', 'state', 'country', 'phone', 'email', 'status']));

        return redirect()->route('branches.index')->with('success', 'Branch created.');
    }

    public function show($id)
    {
        $branch = Branch::findOrFail($id);
        return view('branches.show', compact('branch'));
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'status' => 'required|in:active,inactive',
        ]);

        $branch->update($request->only(['name', 'code', 'address', 'city', 'state', 'country', 'phone', 'email', 'status']));

        return redirect()->route('branches.index')->with('success', 'Branch updated.');
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Branch deleted.');
    }

    public function cards()
    {
        $user = $this->currentUser();

        $branches = $user->isSuperAdmin()
            ? Branch::active()->orderBy('name')->get()
            : Branch::whereKey($user->branch_id)->orderBy('name')->get();

        return view('branches.cards', compact('branches'));
    }

    public function dashboard(Branch $branch)
    {
        $user = Auth::user();

        if (! $user->hasRole('super_admin') && $user->branch_id !== $branch->id) {
            abort(403, 'Unauthorized access to branch dashboard.');
        }

        $patientsCount = $branch->patients()->count();
        $appointmentsCount = $branch->appointments()->count();
        $visitsCount = $branch->visits()->count();
        $medicalRecordsCount = MedicalRecord::whereHas('visit', fn($q) => $q->where('branch_id', $branch->id))->count();
        $hivRecordsCount = HivRecord::whereHas('visit', fn($q) => $q->where('branch_id', $branch->id))->count();
        
        // Inventory statistics
        $inventoryCount = Inventory::where('branch_id', $branch->id)->count();
        $inventoryInStore = Inventory::where('branch_id', $branch->id)->where('status', 'in_store')->count();
        $inventoryAssigned = Inventory::where('branch_id', $branch->id)->where('status', 'assigned')->count();
        $inventoryDisposed = Inventory::where('branch_id', $branch->id)->where('status', 'disposed')->count();

        // Financial statistics
        $totalIncome = Bill::whereHas('visit', fn($q) => $q->where('branch_id', $branch->id))
            ->sum('paid') ?? 0;
        $totalBilled = Bill::whereHas('visit', fn($q) => $q->where('branch_id', $branch->id))
            ->sum('amount') ?? 0;
        $totalExpenses = Expense::where('branch_id', $branch->id)->sum('amount') ?? 0;
        $totalPharmacySales = Sale::whereHas('pharmacy', fn($q) => $q->where('branch_id', $branch->id))
            ->where('status', '!=', 'voided')
            ->sum('total_price') ?? 0;
        
        // Monthly expenses breakdown
        $expensesByCategory = Expense::where('branch_id', $branch->id)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        // Monthly financial data for charts (last 12 months)
        $monthlyFinancials = [];
        for ($i = 11; $i >= 0; $i--) {
            $startDate = now()->subMonths($i)->startOfMonth();
            $endDate = now()->subMonths($i)->endOfMonth();
            
            $monthIncome = Bill::whereHas('visit', fn($q) => $q->where('branch_id', $branch->id))
                ->whereBetween('billed_at', [$startDate, $endDate])
                ->sum('paid') ?? 0;
            
            $monthExpenses = Expense::where('branch_id', $branch->id)
                ->whereBetween('paid_at', [$startDate, $endDate])
                ->sum('amount') ?? 0;
            
            $monthlyFinancials[] = [
                'month' => $startDate->format('M Y'),
                'income' => (float) $monthIncome,
                'expenses' => (float) $monthExpenses,
            ];
        }

        // Audit log stats
        $logins = AuditLog::whereHas('user', fn($q) => $q->whereHas('branch', fn($sq) => $sq->where('id', $branch->id)))
            ->where('action_type', 'login')
            ->count();
        $failedLogins = AuditLog::where('login_status', 'failed')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $branchCalendarEvents = Appointment::where('branch_id', $branch->id)
            ->whereBetween('scheduled_at', [now()->startOfDay(), now()->addDays(14)->endOfDay()])
            ->with(['patient', 'doctor'])
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn($appointment) => [
                'id' => $appointment->id,
                'title' => ($appointment->patient?->full_name ?? 'Patient') . ' / ' . ($appointment->doctor?->name ?? 'TBA'),
                'start' => $appointment->scheduled_at->toIso8601String(),
                'end' => $appointment->scheduled_at->copy()->addMinutes($appointment->duration ?? 30)->toIso8601String(),
                'status' => $appointment->status,
                'service_type' => $appointment->service_type ?: 'General',
            ]);

        return view('branches.dashboard', compact(
            'branch',
            'patientsCount',
            'appointmentsCount',
            'visitsCount',
            'medicalRecordsCount',
            'hivRecordsCount',
            'inventoryCount',
            'inventoryInStore',
            'inventoryAssigned',
            'inventoryDisposed',
            'totalIncome',
            'totalBilled',
            'totalExpenses',
            'totalPharmacySales',
            'expensesByCategory',
            'monthlyFinancials',
            'logins',
            'failedLogins',
            'branchCalendarEvents'
        ));
    }

    public function adminDashboard()
    {
        $branchesCount = Schema::hasTable('branches') ? Branch::count() : 0;
        $usersCount = Schema::hasTable('users') ? User::count() : 0;
        $rolesCount = Schema::hasTable('roles') ? Role::count() : 0;
        $patientsCount = Schema::hasTable('patients') ? Patient::count() : 0;
        $appointmentsCount = Schema::hasTable('appointments') ? Appointment::count() : 0;
        $visitsCount = Schema::hasTable('visits') ? Visit::count() : 0;
        $medicalRecordsCount = Schema::hasTable('medical_records') ? MedicalRecord::count() : 0;
        $hivRecordsCount = Schema::hasTable('hiv_records') ? HivRecord::count() : 0;
        $pharmaciesCount = Schema::hasTable('pharmacies') ? Pharmacy::count() : 0;
        $productsCount = Schema::hasTable('products') ? Product::count() : 0;
        $documentsCount = Schema::hasTable('documents') ? Document::count() : 0;
        $messagesCount = Schema::hasTable('messages') ? Message::count() : 0;
        $inventoryReceivedCount = Schema::hasTable('inventories') ? Inventory::count() : 0;
        $inventoryInStoreCount = Schema::hasTable('inventories') ? Inventory::where('status', Inventory::STATUS_IN_STORE)->count() : 0;
        $inventoryInUseCount = Schema::hasTable('inventories') ? Inventory::where('status', Inventory::STATUS_ASSIGNED)->count() : 0;
        $inventoryNearDisposalCount = Schema::hasTable('inventories') ? Inventory::where('expiry_date', '<=', now()->addDays(30))->count() : 0;
        $hasBillsTable = Schema::hasTable('bills');
        $hasExpensesTable = Schema::hasTable('expenses');
        $hasSalesTable = Schema::hasTable('sales');
        $salesHasStatus = $hasSalesTable && Schema::hasColumn('sales', 'status');
        $expensesHaveStatus = $hasExpensesTable && Schema::hasColumn('expenses', 'status');

        $sumBillCollections = function (?callable $scope = null) use ($hasBillsTable): float {
            if (! $hasBillsTable) {
                return 0.0;
            }

            $query = Bill::query();

            if ($scope) {
                $scope($query);
            }

            return (float) $query->sum('paid');
        };

        $sumBillAmounts = function (?callable $scope = null) use ($hasBillsTable): float {
            if (! $hasBillsTable) {
                return 0.0;
            }

            $query = Bill::query();

            if ($scope) {
                $scope($query);
            }

            return (float) $query->sum('amount');
        };

        $sumSales = function (?callable $scope = null) use ($hasSalesTable, $salesHasStatus): float {
            if (! $hasSalesTable) {
                return 0.0;
            }

            $query = Sale::query();

            if ($salesHasStatus) {
                $query->where('status', 'completed');
            }

            if ($scope) {
                $scope($query);
            }

            return (float) $query->sum('total_price');
        };

        $sumExpenses = function (?callable $scope = null) use ($hasExpensesTable, $expensesHaveStatus): float {
            if (! $hasExpensesTable) {
                return 0.0;
            }

            $query = Expense::query();

            if ($expensesHaveStatus) {
                $query->where('status', 'paid');
            }

            if ($scope) {
                $scope($query);
            }

            return (float) $query->sum('amount');
        };

        // Financial Statistics
        $patientCollections = $sumBillCollections();
        $totalPharmacySales = $sumSales();
        $totalIncome = $patientCollections + $totalPharmacySales;
        $totalBilled = $sumBillAmounts();
        $totalOutstanding = max($totalBilled - $patientCollections, 0);
        $totalExpenses = $sumExpenses();
        $netIncome = $totalIncome - $totalExpenses;

        // Monthly expenses breakdown
        $expensesByCategory = $hasExpensesTable
            ? Expense::when($expensesHaveStatus, fn($query) => $query->where('status', 'paid'))
                ->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->orderByDesc('total')
                ->get()
            : collect();

        // Monthly financial data for charts (last 12 months) - all branches
        $monthlyFinancials = [];
        for ($i = 11; $i >= 0; $i--) {
            $startDate = now()->subMonths($i)->startOfMonth();
            $endDate = now()->subMonths($i)->endOfMonth();

            $monthIncome = $sumBillCollections(
                fn($query) => $query->whereBetween('billed_at', [$startDate, $endDate])
            ) + $sumSales(
                fn($query) => $query->whereBetween('sale_date', [$startDate, $endDate])
            );

            $monthExpenses = $sumExpenses(
                fn($query) => $query->whereBetween('paid_at', [$startDate, $endDate])
            );

            $monthlyFinancials[] = [
                'month' => $startDate->format('M Y'),
                'income' => $monthIncome,
                'expenses' => $monthExpenses,
            ];
        }

        // Audit Log Statistics
        $totalLogins = Schema::hasTable('audit_logs') 
            ? AuditLog::where('action_type', 'login')->count() 
            : 0;
        $successfulLogins = Schema::hasTable('audit_logs') 
            ? AuditLog::where('action_type', 'login')->where('login_status', 'success')->count() 
            : 0;
        $failedLogins = Schema::hasTable('audit_logs') 
            ? AuditLog::where('login_status', 'failed')->where('created_at', '>=', now()->subDays(30))->count() 
            : 0;
        $activeSessions = Schema::hasTable('audit_logs') 
            ? AuditLog::activeSessions()->count() 
            : 0;

        $hasAdmissionsTable = Schema::hasTable('admissions');
        $hasBedsTable = Schema::hasTable('beds');
        $hasWardsTable = Schema::hasTable('wards');
        $hasLabTestsTable = Schema::hasTable('lab_tests');
        $hasPrescriptionOrdersTable = Schema::hasTable('prescription_orders');
        $hasEmployeesTable = Schema::hasTable('employees');
        $hasAttendanceTable = Schema::hasTable('staff_attendances');
        $hasPayrollRunsTable = Schema::hasTable('payroll_runs');
        $hasAppraisalsTable = Schema::hasTable('staff_appraisals');
        $hasRequisitionsTable = Schema::hasTable('requisitions');
        $hasPurchaseOrdersTable = Schema::hasTable('purchase_orders');

        // Live operations and patient flow
        $todayVisitsCount = Schema::hasTable('visits') ? Visit::whereDate('visit_date', today())->count() : 0;
        $openVisitsCount = Schema::hasTable('visits') ? Visit::where('status', 'open')->count() : 0;
        $completedVisitsToday = Schema::hasTable('visits') ? Visit::where('workflow_stage', Visit::STAGE_COMPLETED)->whereDate('completed_at', today())->count() : 0;
        $queueStageCounts = Schema::hasTable('visits')
            ? collect(Visit::WORKFLOW_STAGES)->mapWithKeys(fn ($label, $stage) => [
                $label => Visit::where('status', 'open')->where('workflow_stage', $stage)->count(),
            ])
            : collect(Visit::WORKFLOW_STAGES)->mapWithKeys(fn ($label) => [$label => 0]);

        $appointmentsToday = Schema::hasTable('appointments') ? Appointment::whereDate('scheduled_at', today())->count() : 0;
        $appointmentsNext7Days = Schema::hasTable('appointments') ? Appointment::whereBetween('scheduled_at', [now()->startOfDay(), now()->addDays(7)->endOfDay()])->count() : 0;

        // Inpatient command centre
        $activeAdmissionsCount = $hasAdmissionsTable ? Admission::whereIn('status', [Admission::STATUS_ADMITTED, Admission::STATUS_READY, Admission::STATUS_PENDING_CLEARANCE])->count() : 0;
        $admissionsToday = $hasAdmissionsTable ? Admission::whereDate('admitted_at', today())->count() : 0;
        $dischargesToday = $hasAdmissionsTable ? Admission::whereNotNull('discharged_at')->whereDate('discharged_at', today())->count() : 0;
        $readyForDischarge = $hasAdmissionsTable ? Admission::where('status', Admission::STATUS_READY)->count() : 0;
        $pendingDischargeClearance = $hasAdmissionsTable ? Admission::whereIn('status', [Admission::STATUS_READY, Admission::STATUS_PENDING_CLEARANCE])
            ->where(fn ($query) => $query->where('nursing_cleared', false)->orWhere('pharmacy_cleared', false)->orWhere('billing_cleared', false))
            ->count() : 0;
        $totalBeds = $hasBedsTable ? Bed::count() : 0;
        $availableBeds = $hasBedsTable ? Bed::where('status', Bed::STATUS_AVAILABLE)->count() : 0;
        $occupiedBeds = $hasBedsTable ? Bed::where('status', Bed::STATUS_OCCUPIED)->count() : 0;
        $cleaningBeds = $hasBedsTable ? Bed::where('status', Bed::STATUS_CLEANING)->count() : 0;
        $maintenanceBeds = $hasBedsTable ? Bed::where('status', Bed::STATUS_MAINTENANCE)->count() : 0;
        $bedOccupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;
        $admissionsByWard = $hasAdmissionsTable && $hasWardsTable
            ? Admission::query()
                ->join('wards', 'admissions.ward_id', '=', 'wards.id')
                ->whereIn('admissions.status', [Admission::STATUS_ADMITTED, Admission::STATUS_READY, Admission::STATUS_PENDING_CLEARANCE])
                ->select('wards.name', DB::raw('COUNT(*) as total'))
                ->groupBy('wards.name')
                ->orderByDesc('total')
                ->limit(8)
                ->get()
            : collect();

        $monthlyAdmissionsFlow = [];
        for ($i = 5; $i >= 0; $i--) {
            $startDate = now()->subMonths($i)->startOfMonth();
            $endDate = now()->subMonths($i)->endOfMonth();
            $monthlyAdmissionsFlow[] = [
                'month' => $startDate->format('M Y'),
                'admissions' => $hasAdmissionsTable ? Admission::whereBetween('admitted_at', [$startDate, $endDate])->count() : 0,
                'discharges' => $hasAdmissionsTable ? Admission::whereBetween('discharged_at', [$startDate, $endDate])->count() : 0,
            ];
        }

        // Clinical workload
        $pendingLabTests = $hasLabTestsTable ? LabTest::whereIn('status', ['ordered', 'in_progress'])->count() : 0;
        $completedLabTestsToday = $hasLabTestsTable ? LabTest::where('status', 'completed')->whereDate('completed_at', today())->count() : 0;
        $labStatusCounts = $hasLabTestsTable
            ? LabTest::select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')
            : collect();
        $topLabTests = $hasLabTestsTable
            ? LabTest::select('test_type', DB::raw('COUNT(*) as total'))->groupBy('test_type')->orderByDesc('total')->limit(8)->get()
            : collect();

        $pendingPrescriptions = $hasPrescriptionOrdersTable ? PrescriptionOrder::whereIn('status', ['pending', 'partially_dispensed'])->count() : 0;
        $dispensedToday = $hasPrescriptionOrdersTable ? PrescriptionOrder::whereNotNull('dispensed_at')->whereDate('dispensed_at', today())->count() : 0;
        $prescriptionStatusCounts = $hasPrescriptionOrdersTable
            ? PrescriptionOrder::select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')
            : collect();
        $lowStockProducts = Schema::hasTable('products') ? Product::lowStock()->count() : 0;
        $outOfStockProducts = Schema::hasTable('products') ? Product::where('quantity', '<=', 0)->count() : 0;
        $expiringProducts = Schema::hasTable('products') ? Product::expiringSoon(30)->count() : 0;

        // Billing operations
        $billStatusCounts = $hasBillsTable
            ? Bill::select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')
            : collect();
        $billingBlockedDischarges = $hasAdmissionsTable ? Admission::whereIn('status', [Admission::STATUS_READY, Admission::STATUS_PENDING_CLEARANCE])->where('billing_cleared', false)->count() : 0;
        $outstandingAging = [
            '0-30 days' => 0.0,
            '31-60 days' => 0.0,
            '61-90 days' => 0.0,
            '90+ days' => 0.0,
        ];
        if ($hasBillsTable) {
            Bill::whereIn('status', ['unpaid', 'partial'])->get()->each(function (Bill $bill) use (&$outstandingAging) {
                $balance = max((float) $bill->amount - (float) $bill->paid, 0);
                $age = $bill->billed_at ? $bill->billed_at->diffInDays(now()) : 0;
                if ($age <= 30) {
                    $outstandingAging['0-30 days'] += $balance;
                } elseif ($age <= 60) {
                    $outstandingAging['31-60 days'] += $balance;
                } elseif ($age <= 90) {
                    $outstandingAging['61-90 days'] += $balance;
                } else {
                    $outstandingAging['90+ days'] += $balance;
                }
            });
        }

        // Staff, procurement, and branch performance
        $activeStaffCount = $hasEmployeesTable ? Employee::where('status', 'active')->count() : 0;
        $staffOnLeaveCount = $hasEmployeesTable ? Employee::where('status', 'on_leave')->count() : 0;
        $attendanceTodayCount = $hasAttendanceTable ? StaffAttendance::whereDate('work_date', today())->count() : 0;
        $lateAttendanceToday = $hasAttendanceTable ? StaffAttendance::whereDate('work_date', today())->where('status', 'late')->count() : 0;
        $pendingAppraisals = $hasAppraisalsTable ? StaffAppraisal::whereIn('status', ['draft', 'pending'])->count() : 0;
        $payrollStatusCounts = $hasPayrollRunsTable
            ? PayrollRun::select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')
            : collect();
        $staffByRole = Schema::hasTable('roles') && Schema::hasTable('model_has_roles')
            ? DB::table('roles')
                ->leftJoin('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select('roles.name', DB::raw('COUNT(model_has_roles.model_id) as users_count'))
                ->groupBy('roles.id', 'roles.name')
                ->orderByDesc('users_count')
                ->limit(8)
                ->get()
            : collect();
        $staffByBranch = Schema::hasTable('branches') && $hasEmployeesTable
            ? Branch::withCount('users')->orderByDesc('users_count')->limit(8)->get()
            : collect();

        $requisitionStatusCounts = $hasRequisitionsTable
            ? Requisition::select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')
            : collect();
        $purchaseOrderStatusCounts = $hasPurchaseOrdersTable
            ? PurchaseOrder::select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status')
            : collect();
        $pendingRequisitions = $hasRequisitionsTable ? Requisition::whereNotIn('status', ['approved', 'rejected', 'cancelled'])->count() : 0;
        $pendingPurchaseOrders = $hasPurchaseOrdersTable ? PurchaseOrder::whereIn('status', ['draft', 'pending', 'ordered'])->count() : 0;

        $branchPerformance = Schema::hasTable('branches')
            ? Branch::active()->withCount(['patients', 'visits', 'appointments'])->orderBy('name')->get()->map(function (Branch $branch) use ($hasAdmissionsTable, $hasBillsTable) {
                $revenue = $hasBillsTable
                    ? Bill::whereHas('patient', fn ($query) => $query->where('branch_id', $branch->id))->sum('paid')
                    : 0;
                return [
                    'branch' => $branch->name,
                    'patients' => $branch->patients_count,
                    'visits' => $branch->visits_count,
                    'appointments' => $branch->appointments_count,
                    'admissions' => $hasAdmissionsTable ? Admission::where('branch_id', $branch->id)->count() : 0,
                    'revenue' => (float) $revenue,
                ];
            })
            : collect();

        $failedLoginTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $failedLoginTrend[] = [
                'day' => now()->subDays($i)->format('M d'),
                'failed' => Schema::hasTable('audit_logs') ? AuditLog::where('login_status', 'failed')->whereDate('created_at', $date)->count() : 0,
            ];
        }

        $adminCalendarEvents = Appointment::whereBetween('scheduled_at', [now()->startOfDay(), now()->addDays(14)->endOfDay()])
            ->with(['patient', 'doctor', 'branch'])
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn($appointment) => [
                'id' => $appointment->id,
                'title' => ($appointment->patient?->full_name ?? 'Patient') . ' / ' . ($appointment->doctor?->name ?? 'TBA'),
                'start' => $appointment->scheduled_at->toIso8601String(),
                'end' => $appointment->scheduled_at->copy()->addMinutes($appointment->duration ?? 30)->toIso8601String(),
                'status' => $appointment->status,
                'service_type' => $appointment->service_type ?: 'General',
                'branch' => $appointment->branch?->name,
            ]);

        return view('admin.dashboard', compact(
            'branchesCount',
            'usersCount',
            'rolesCount',
            'patientsCount',
            'appointmentsCount',
            'visitsCount',
            'medicalRecordsCount',
            'hivRecordsCount',
            'pharmaciesCount',
            'productsCount',
            'documentsCount',
            'messagesCount',
            'inventoryReceivedCount',
            'inventoryInStoreCount',
            'inventoryInUseCount',
            'inventoryNearDisposalCount',
            'patientCollections',
            'totalIncome',
            'totalBilled',
            'totalOutstanding',
            'totalExpenses',
            'totalPharmacySales',
            'netIncome',
            'expensesByCategory',
            'monthlyFinancials',
            'totalLogins',
            'successfulLogins',
            'failedLogins',
            'activeSessions',
            'todayVisitsCount',
            'openVisitsCount',
            'completedVisitsToday',
            'queueStageCounts',
            'appointmentsToday',
            'appointmentsNext7Days',
            'activeAdmissionsCount',
            'admissionsToday',
            'dischargesToday',
            'readyForDischarge',
            'pendingDischargeClearance',
            'totalBeds',
            'availableBeds',
            'occupiedBeds',
            'cleaningBeds',
            'maintenanceBeds',
            'bedOccupancyRate',
            'admissionsByWard',
            'monthlyAdmissionsFlow',
            'pendingLabTests',
            'completedLabTestsToday',
            'labStatusCounts',
            'topLabTests',
            'pendingPrescriptions',
            'dispensedToday',
            'prescriptionStatusCounts',
            'lowStockProducts',
            'outOfStockProducts',
            'expiringProducts',
            'billStatusCounts',
            'billingBlockedDischarges',
            'outstandingAging',
            'activeStaffCount',
            'staffOnLeaveCount',
            'attendanceTodayCount',
            'lateAttendanceToday',
            'pendingAppraisals',
            'payrollStatusCounts',
            'staffByRole',
            'staffByBranch',
            'requisitionStatusCounts',
            'purchaseOrderStatusCounts',
            'pendingRequisitions',
            'pendingPurchaseOrders',
            'branchPerformance',
            'failedLoginTrend',
            'adminCalendarEvents'
        ));
    }
}

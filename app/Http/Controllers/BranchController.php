<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesBranchContext;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Bill;
use App\Models\Branch;
use App\Models\Document;
use App\Models\Expense;
use App\Models\HivRecord;
use App\Models\Inventory;
use App\Models\MedicalRecord;
use App\Models\Message;
use App\Models\Patient;
use App\Models\Pharmacy;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sale;
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
            'adminCalendarEvents'
        ));
    }
}

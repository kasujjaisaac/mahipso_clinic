<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use Illuminate\Http\Request;

class PharmacyAnalyticsController extends Controller
{
    public function dashboard(Pharmacy $pharmacy)
    {
        $this->authorize('view', $pharmacy);

        // Get date range from request or default to current month
        $from = request('from') ? \Carbon\Carbon::parse(request('from')) : now()->startOfMonth();
        $to = request('to') ? \Carbon\Carbon::parse(request('to')) : now()->endOfMonth();

        // Key metrics
        $metrics = [
            'total_revenue' => $pharmacy->sales()
                ->where('status', 'completed')
                ->whereBetween('sale_date', [$from, $to])
                ->sum('total_price'),
            'total_sales' => $pharmacy->sales()
                ->where('status', 'completed')
                ->whereBetween('sale_date', [$from, $to])
                ->count(),
            'low_stock_count' => $pharmacy->products()->lowStock()->count(),
            'expiring_count' => $pharmacy->products()->expiringSoon()->count(),
            'expired_count' => $pharmacy->products()->expired()->count(),
            'inventory_value' => $pharmacy->getInventoryValueAttribute(),
            'total_products' => $pharmacy->products()->count(),
            'active_products' => $pharmacy->products()->active()->count(),
        ];

        // Top selling products
        $topProducts = $pharmacy->sales()
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$from, $to])
            ->with('product')
            ->get()
            ->groupBy('product_id')
            ->map(function ($sales) {
                return [
                    'product' => $sales[0]->product->name,
                    'quantity' => $sales->sum('quantity'),
                    'revenue' => $sales->sum('total_price'),
                ];
            })
            ->sortByDesc('revenue')
            ->take(10)
            ->values();

        // Daily revenue trend
        $dailyRevenue = $pharmacy->sales()
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$from, $to])
            ->get()
            ->groupBy(function ($sale) {
                return $sale->sale_date->format('Y-m-d');
            })
            ->map(function ($sales) {
                return $sales->sum('total_price');
            })
            ->sortKeys();

        // Low stock products
        $lowStockProducts = $pharmacy->products()
            ->lowStock()
            ->with('category')
            ->orderBy('quantity')
            ->take(10)
            ->get();

        // Expiring products
        $expiringProducts = $pharmacy->products()
            ->expiringSoon()
            ->with('category')
            ->orderBy('expiry_date')
            ->take(10)
            ->get();

        return view('pharmacies.analytics', compact(
            'pharmacy',
            'metrics',
            'topProducts',
            'dailyRevenue',
            'lowStockProducts',
            'expiringProducts',
            'from',
            'to'
        ));
    }

    public function lowStockReport(Pharmacy $pharmacy)
    {
        $this->authorize('view', $pharmacy);

        $products = $pharmacy->products()
            ->lowStock()
            ->with('category')
            ->orderBy('quantity')
            ->paginate(20);

        return view('pharmacies.reports.low-stock', compact('pharmacy', 'products'));
    }

    public function expiryReport(Pharmacy $pharmacy)
    {
        $this->authorize('view', $pharmacy);

        $expired = $pharmacy->products()
            ->expired()
            ->with('category')
            ->orderBy('expiry_date')
            ->get();

        $expiringSoon = $pharmacy->products()
            ->expiringSoon()
            ->with('category')
            ->orderBy('expiry_date')
            ->get();

        return view('pharmacies.reports.expiry', compact('pharmacy', 'expired', 'expiringSoon'));
    }

    public function revenueReport(Pharmacy $pharmacy, Request $request)
    {
        $this->authorize('view', $pharmacy);

        $from = $request->filled('from') ? \Carbon\Carbon::parse($request->from) : now()->startOfMonth();
        $to = $request->filled('to') ? \Carbon\Carbon::parse($request->to) : now()->endOfMonth();

        $sales = $pharmacy->sales()
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$from, $to])
            ->with('product', 'soldBy')
            ->latest('sale_date')
            ->paginate(20);

        $totalRevenue = $sales->sum('total_price');

        return view('pharmacies.reports.revenue', compact('pharmacy', 'sales', 'totalRevenue', 'from', 'to'));
    }
}

@extends('layouts.app')
@section('title', 'Financial Dashboard')
@section('section', 'Financial')
@section('page_title', 'Financial Dashboard')
@section('content')
<div class="panel">
    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 class="section-title" style="font-size:1.2rem; font-weight:600; margin-bottom:0.2rem;">Financial Overview</h2>
            <p class="table-meta" style="font-size:0.95rem; color:#888;">Comprehensive financial management and analytics.</p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <select id="month-select" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
            <select id="year-select" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="financial-summary" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <!-- Total Income -->
        <div class="summary-card income-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(16, 185, 129, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h3 style="margin: 0; font-size: 0.9rem; opacity: 0.9;">Total Income</h3>
                    <div style="font-size: 2rem; font-weight: 700; margin: 0.5rem 0;">${{ number_format($totalIncome, 2) }}</div>
                    <p style="margin: 0; font-size: 0.8rem; opacity: 0.8;">This month</p>
                </div>
                <div style="font-size: 2rem; opacity: 0.7;">💰</div>
            </div>
        </div>

        <!-- Total Expenses -->
        <div class="summary-card expense-card" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h3 style="margin: 0; font-size: 0.9rem; opacity: 0.9;">Total Expenses</h3>
                    <div style="font-size: 2rem; font-weight: 700; margin: 0.5rem 0;">${{ number_format($totalExpenses, 2) }}</div>
                    <p style="margin: 0; font-size: 0.8rem; opacity: 0.8;">This month</p>
                </div>
                <div style="font-size: 2rem; opacity: 0.7;">💸</div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="summary-card profit-card" style="background: linear-gradient(135deg, {{ $totalIncome - $totalExpenses >= 0 ? '#3b82f6 0%, #2563eb 100%' : '#f59e0b 0%, #d97706 100%' }}); color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h3 style="margin: 0; font-size: 0.9rem; opacity: 0.9;">Net {{ $totalIncome - $totalExpenses >= 0 ? 'Profit' : 'Loss' }}</h3>
                    <div style="font-size: 2rem; font-weight: 700; margin: 0.5rem 0;">${{ number_format(abs($totalIncome - $totalExpenses), 2) }}</div>
                    <p style="margin: 0; font-size: 0.8rem; opacity: 0.8;">This month</p>
                </div>
                <div style="font-size: 2rem; opacity: 0.7;">{{ $totalIncome - $totalExpenses >= 0 ? '📈' : '📉' }}</div>
            </div>
        </div>

        <!-- Pending Expenses -->
        <div class="summary-card pending-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(245, 158, 11, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div>
                    <h3 style="margin: 0; font-size: 0.9rem; opacity: 0.9;">Pending Expenses</h3>
                    <div style="font-size: 2rem; font-weight: 700; margin: 0.5rem 0;">${{ number_format($pendingExpenses, 2) }}</div>
                    <p style="margin: 0; font-size: 0.8rem; opacity: 0.8;">Due this month</p>
                </div>
                <div style="font-size: 2rem; opacity: 0.7;">⏰</div>
            </div>
        </div>
    </div>

    <!-- Charts and Detailed Breakdown -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">

        <!-- Monthly Trend Chart -->
        <div class="chart-container" style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">Income vs Expenses Trend</h3>
            <canvas id="financialChart" width="400" height="200"></canvas>
        </div>

        <!-- Expense Breakdown -->
        <div class="expense-breakdown" style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">Expense Breakdown</h3>
            <div class="expense-categories" style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($expensesByCategory as $category)
                <div class="category-item" style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem; background: #f8fafc; border-radius: 6px;">
                    <span style="font-weight: 500; color: #374151; text-transform: capitalize;">{{ str_replace('_', ' ', $category->category) }}</span>
                    <span style="font-weight: 600; color: #1f2937;">${{ number_format($category->total, 2) }}</span>
                </div>
                @endforeach
                @if($expensesByCategory->isEmpty())
                <div style="text-align: center; color: #6b7280; padding: 2rem;">
                    No expenses recorded this month
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions" style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 2rem;">
        <h3 style="margin: 0 0 1rem 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">Quick Actions</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <a href="{{ route('financial.income') }}" class="action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 8px; text-decoration: none; color: #0c4a6e; transition: all 0.2s;">
                <div style="font-size: 1.5rem;">💰</div>
                <div>
                    <div style="font-weight: 600;">Manage Income</div>
                    <div style="font-size: 0.8rem; opacity: 0.8;">Patient bills & pharmacy sales</div>
                </div>
            </a>

            <a href="{{ route('financial.expenditure') }}" class="action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #fef2f2; border: 1px solid #ef4444; border-radius: 8px; text-decoration: none; color: #7f1d1d; transition: all 0.2s;">
                <div style="font-size: 1.5rem;">💸</div>
                <div>
                    <div style="font-weight: 600;">Manage Expenses</div>
                    <div style="font-size: 0.8rem; opacity: 0.8;">Operational costs & utilities</div>
                </div>
            </a>

            <a href="{{ route('expenses.create') }}" class="action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f0fdf4; border: 1px solid #22c55e; border-radius: 8px; text-decoration: none; color: #14532d; transition: all 0.2s;">
                <div style="font-size: 1.5rem;">➕</div>
                <div>
                    <div style="font-weight: 600;">Add Expense</div>
                    <div style="font-size: 0.8rem; opacity: 0.8;">Record new operational cost</div>
                </div>
            </a>

            <a href="{{ route('reporting.index') }}" class="action-card" style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #fefce8; border: 1px solid #eab308; border-radius: 8px; text-decoration: none; color: #713f12; transition: all 0.2s;">
                <div style="font-size: 1.5rem;">📊</div>
                <div>
                    <div style="font-weight: 600;">Financial Reports</div>
                    <div style="font-size: 0.8rem; opacity: 0.8;">Detailed analytics & insights</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="recent-transactions" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
        <!-- Recent Income -->
        <div class="transactions-section" style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">Recent Income</h3>
                <a href="{{ route('financial.income') }}" style="color: #3b82f6; text-decoration: none; font-size: 0.9rem;">View all</a>
            </div>
            <div class="transaction-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse(array_merge($recentBills->toArray(), $recentSales->toArray()) as $transaction)
                <div class="transaction-item" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f8fafc; border-radius: 6px;">
                    <div>
                        <div style="font-weight: 500; color: #1f2937;">
                            @if(isset($transaction['patient']))
                                {{ $transaction['patient']['full_name'] ?? 'Patient' }}
                            @else
                                {{ $transaction['description'] ?? 'Sale' }}
                            @endif
                        </div>
                        <div style="font-size: 0.8rem; color: #6b7280;">
                            {{ isset($transaction['billed_at']) ? \Carbon\Carbon::parse($transaction['billed_at'])->format('M d, Y') : \Carbon\Carbon::parse($transaction['created_at'])->format('M d, Y') }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 600; color: #10b981;">
                            ${{ number_format($transaction['paid'] ?? $transaction['total_amount'] ?? $transaction['amount'], 2) }}
                        </div>
                        <div style="font-size: 0.8rem; color: #6b7280;">
                            {{ isset($transaction['status']) ? ucfirst($transaction['status']) : 'Completed' }}
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; color: #6b7280; padding: 2rem;">
                    No recent income transactions
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="transactions-section" style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">Recent Expenses</h3>
                <a href="{{ route('financial.expenditure') }}" style="color: #3b82f6; text-decoration: none; font-size: 0.9rem;">View all</a>
            </div>
            <div class="transaction-list" style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($recentExpenses as $expense)
                <div class="transaction-item" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: #f8fafc; border-radius: 6px;">
                    <div>
                        <div style="font-weight: 500; color: #1f2937;">{{ $expense->description }}</div>
                        <div style="font-size: 0.8rem; color: #6b7280;">
                            {{ ucfirst(str_replace('_', ' ', $expense->category)) }}
                            @if($expense->paid_at)
                                • {{ $expense->paid_at->format('M d, Y') }}
                            @endif
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-weight: 600; color: #ef4444;">
                            ${{ number_format($expense->amount, 2) }}
                        </div>
                        <div style="font-size: 0.8rem; color: #6b7280;">
                            {{ ucfirst($expense->status) }}
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; color: #6b7280; padding: 2rem;">
                    No recent expenses
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .financial-summary, .chart-container, .expense-breakdown, .quick-actions, .recent-transactions {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
    }
    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>

<script>
document.getElementById('month-select').addEventListener('change', updateFinancialData);
document.getElementById('year-select').addEventListener('change', updateFinancialData);

function updateFinancialData() {
    const month = document.getElementById('month-select').value;
    const year = document.getElementById('year-select').value;
    window.location.href = `{{ route('financial.index') }}?month=${month}&year=${year}`;
}

// Financial Chart
const ctx = document.getElementById('financialChart').getContext('2d');
const financialChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json(collect($monthlyData)->pluck('month')),
        datasets: [{
            label: 'Income',
            data: @json(collect($monthlyData)->pluck('income')),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Expenses',
            data: @json(collect($monthlyData)->pluck('expenses')),
            borderColor: '#ef4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endsection
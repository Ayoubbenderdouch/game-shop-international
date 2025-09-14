@extends('admin.layout')

@section('title', 'Reports Dashboard')
@section('page-title', 'Reports')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-white mb-2">Reports Dashboard</h1>
        <p class="text-gray-400">Analytics and business insights</p>
    </div>
    <div class="flex items-center space-x-3">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex space-x-2">
            <input type="date" name="start_date" value="{{ $startDate }}"
                   class="px-3 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 text-sm">
            <input type="date" name="end_date" value="{{ $endDate }}"
                   class="px-3 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 text-sm">
            <button type="submit" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                <i class="fas fa-filter mr-2"></i>Apply
            </button>
        </form>
    </div>
</div>

<!-- Revenue Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Total Revenue</span>
            <i class="fas fa-dollar-sign text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">${{ number_format($totalRevenue ?? 0, 2) }}</p>
        <p class="text-white/60 text-sm mt-2">
            <i class="fas fa-{{ $revenueGrowth >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
            {{ number_format(abs($revenueGrowth ?? 0), 1) }}% from last period
        </p>
    </div>

    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Total Orders</span>
            <i class="fas fa-shopping-cart text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $totalOrders ?? 0 }}</p>
        <p class="text-white/60 text-sm mt-2">
            {{ $completedOrders ?? 0 }} completed
        </p>
    </div>

    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Avg Order Value</span>
            <i class="fas fa-chart-line text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">${{ number_format($averageOrderValue ?? 0, 2) }}</p>
    </div>

    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">New Customers</span>
            <i class="fas fa-user-plus text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $newCustomers ?? 0 }}</p>
        <p class="text-white/60 text-sm mt-2">
            {{ $returningCustomers ?? 0 }} returning
        </p>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Revenue Chart -->
    <div class="bg-dark-card rounded-xl border border-dark-border p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Revenue Trend</h2>
        <div style="position: relative; height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="bg-dark-card rounded-xl border border-dark-border p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Category Performance</h2>
        <div style="position: relative; height: 300px;">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<!-- Top Products -->
<div class="bg-dark-card rounded-xl border border-dark-border mb-6">
    <div class="p-6 border-b border-dark-border">
        <h2 class="text-lg font-semibold text-white">Top Selling Products</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark-bg border-b border-dark-border">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Units Sold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Trend</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @foreach($topProducts ?? [] as $product)
                <tr class="hover:bg-dark-bg transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover mr-3">
                            @else
                            <div class="w-10 h-10 bg-gray-700 rounded-lg mr-3"></div>
                            @endif
                            <div>
                                <p class="text-white font-medium">{{ $product->name }}</p>
                                <p class="text-gray-400 text-sm">{{ $product->category->name ?? 'Uncategorized' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-white">{{ $product->total_sold ?? 0 }}</td>
                    <td class="px-6 py-4 text-white font-semibold">${{ number_format($product->total_revenue ?? 0, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="text-green-400">
                            <i class="fas fa-arrow-up"></i> Trending
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <a href="{{ route('admin.reports.sales') }}" class="bg-dark-card rounded-xl border border-dark-border p-6 hover:border-primary-blue transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-blue-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-bar text-blue-400 text-xl"></i>
            </div>
            <i class="fas fa-arrow-right text-gray-400"></i>
        </div>
        <h3 class="text-white font-semibold">Sales Report</h3>
        <p class="text-gray-400 text-sm mt-1">Detailed sales analysis</p>
    </a>

    <a href="{{ route('admin.reports.products') }}" class="bg-dark-card rounded-xl border border-dark-border p-6 hover:border-primary-blue transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-purple-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-box text-purple-400 text-xl"></i>
            </div>
            <i class="fas fa-arrow-right text-gray-400"></i>
        </div>
        <h3 class="text-white font-semibold">Product Report</h3>
        <p class="text-gray-400 text-sm mt-1">Product performance metrics</p>
    </a>

    <a href="{{ route('admin.reports.customers') }}" class="bg-dark-card rounded-xl border border-dark-border p-6 hover:border-primary-blue transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-green-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-green-400 text-xl"></i>
            </div>
            <i class="fas fa-arrow-right text-gray-400"></i>
        </div>
        <h3 class="text-white font-semibold">Customer Report</h3>
        <p class="text-gray-400 text-sm mt-1">Customer insights & behavior</p>
    </a>

    <a href="{{ route('admin.reports.export') }}" class="bg-dark-card rounded-xl border border-dark-border p-6 hover:border-primary-blue transition-all">
        <div class="flex items-center justify-between mb-3">
            <div class="w-12 h-12 bg-orange-600/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-download text-orange-400 text-xl"></i>
            </div>
            <i class="fas fa-arrow-right text-gray-400"></i>
        </div>
        <h3 class="text-white font-semibold">Export Data</h3>
        <p class="text-gray-400 text-sm mt-1">Download reports</p>
    </a>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($dailyRevenue->pluck('date') ?? []) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($dailyRevenue->pluck('revenue') ?? []) !!},
            borderColor: '#00ff88',
            backgroundColor: 'rgba(0, 255, 136, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#9ca3af',
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#9ca3af'
                }
            }
        }
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoryPerformance->pluck('name') ?? []) !!},
        datasets: [{
            data: {!! json_encode($categoryPerformance->pluck('revenue') ?? []) !!},
            backgroundColor: [
                '#00ff88',
                '#3b82f6',
                '#8b5cf6',
                '#f59e0b',
                '#ef4444'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    color: '#9ca3af'
                }
            }
        }
    }
});
</script>
@endpush

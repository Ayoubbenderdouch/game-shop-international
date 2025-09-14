@extends('admin.layout')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign text-white text-xl"></i>
            </div>
            <span class="text-white/60 text-sm">Today</span>
        </div>
        <h3 class="text-white text-2xl font-bold mb-1">${{ number_format($todayRevenue ?? 0, 2) }}</h3>
        <p class="text-white/80 text-sm">Revenue</p>
        <div class="mt-3 flex items-center text-white/60 text-xs">
            @if(($revenueGrowth ?? 0) >= 0)
                <i class="fas fa-arrow-up text-green-400 mr-1"></i>
            @else
                <i class="fas fa-arrow-down text-red-400 mr-1"></i>
            @endif
            <span>{{ abs($revenueGrowth ?? 0) }}% from yesterday</span>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-shopping-bag text-white text-xl"></i>
            </div>
            <span class="text-white/60 text-sm">Today</span>
        </div>
        <h3 class="text-white text-2xl font-bold mb-1">{{ $todayOrders ?? 0 }}</h3>
        <p class="text-white/80 text-sm">Orders</p>
        <div class="mt-3 flex items-center text-white/60 text-xs">
            @if(($ordersGrowth ?? 0) >= 0)
                <i class="fas fa-arrow-up text-green-400 mr-1"></i>
            @else
                <i class="fas fa-arrow-down text-red-400 mr-1"></i>
            @endif
            <span>{{ abs($ordersGrowth ?? 0) }}% from yesterday</span>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-gamepad text-white text-xl"></i>
            </div>
            <span class="text-white/60 text-sm">Active</span>
        </div>
        <h3 class="text-white text-2xl font-bold mb-1">{{ $activeProducts ?? 0 }}</h3>
        <p class="text-white/80 text-sm">Products</p>
        <div class="mt-3 flex items-center text-white/60 text-xs">
            <i class="fas fa-check-circle text-green-400 mr-1"></i>
            <span>{{ $totalProducts ?? 0 }} total products</span>
        </div>
    </div>

    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-white text-xl"></i>
            </div>
            <span class="text-white/60 text-sm">Total</span>
        </div>
        <h3 class="text-white text-2xl font-bold mb-1">{{ $totalUsers ?? 0 }}</h3>
        <p class="text-white/80 text-sm">Customers</p>
        <div class="mt-3 flex items-center text-white/60 text-xs">
            <i class="fas fa-arrow-up text-green-400 mr-1"></i>
            <span>{{ $newUsersToday ?? 0 }} new today</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-dark-card rounded-xl p-6 border border-dark-border">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">Revenue Overview</h2>
            <select id="revenue-period" class="bg-dark-bg border border-dark-border rounded-lg px-3 py-1 text-sm text-gray-400">
                <option value="7" {{ ($period ?? 30) == 7 ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30" {{ ($period ?? 30) == 30 ? 'selected' : '' }}>Last 30 Days</option>
                <option value="90" {{ ($period ?? 30) == 90 ? 'selected' : '' }}>Last 90 Days</option>
            </select>
        </div>
        <div style="position: relative; height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="bg-dark-card rounded-xl p-6 border border-dark-border">
        <h2 class="text-xl font-bold text-white mb-6">Top Categories</h2>
        <div style="position: relative; height: 200px;">
            <canvas id="categoriesChart"></canvas>
        </div>
        <div class="mt-4 space-y-2">
            @foreach($topCategories ?? [] as $category)
            <div class="flex items-center justify-between">
                <span class="text-gray-400 text-sm">{{ $category->name }}</span>
                <span class="text-white font-semibold">${{ number_format($category->revenue, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-dark-card rounded-xl p-6 border border-dark-border">
        <h2 class="text-xl font-bold text-white mb-4">Quick Actions</h2>
        <div class="space-y-3">
            <a href="{{ route('admin.products.create') }}" class="flex items-center space-x-3 p-3 bg-dark-bg rounded-lg hover:bg-gray-800 transition-all">
                <i class="fas fa-plus text-primary-blue"></i>
                <span class="text-gray-300">Add New Product</span>
            </a>
            <a href="{{ route('admin.api-sync.index') }}" class="flex items-center space-x-3 p-3 bg-dark-bg rounded-lg hover:bg-gray-800 transition-all">
                <i class="fas fa-sync text-primary-blue"></i>
                <span class="text-gray-300">Sync Products from API</span>
            </a>
            <a href="{{ route('admin.pricing-rules.create') }}" class="flex items-center space-x-3 p-3 bg-dark-bg rounded-lg hover:bg-gray-800 transition-all">
                <i class="fas fa-tags text-primary-blue"></i>
                <span class="text-gray-300">Create Pricing Rule</span>
            </a>
            <a href="{{ route('admin.reports.sales') }}" class="flex items-center space-x-3 p-3 bg-dark-bg rounded-lg hover:bg-gray-800 transition-all">
                <i class="fas fa-chart-line text-primary-blue"></i>
                <span class="text-gray-300">View Sales Report</span>
            </a>
        </div>
    </div>

    <div class="bg-dark-card rounded-xl p-6 border border-dark-border">
        <h2 class="text-xl font-bold text-white mb-4">System Status</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-gray-400">API Connection</span>
                @if($apiBalance !== null)
                    <span class="px-2 py-1 text-xs rounded-full bg-green-900/50 text-green-400">Connected</span>
                @else
                    <span class="px-2 py-1 text-xs rounded-full bg-red-900/50 text-red-400">Error</span>
                @endif
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-400">API Balance</span>
                <span class="text-gray-300 text-sm">
                    @if($apiBalance !== null && is_numeric($apiBalance))
                        @if($apiCurrency === 'USD')
                            ${{ number_format($apiBalance, 2) }}
                        @else
                            {{ $apiCurrency }} {{ number_format($apiBalance, 2) }}
                        @endif
                    @else
                        N/A
                    @endif
                </span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-400">Last Sync</span>
                <span class="text-gray-300 text-sm">{{ $lastSync ?? 'Never' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-400">Pending Orders</span>
                <span class="text-yellow-400 font-semibold">{{ $pendingOrders ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-400">Low Stock Products</span>
                <span class="text-orange-400 font-semibold">{{ $lowStockProducts ?? 0 }}</span>
            </div>
        </div>
    </div>

    <div class="bg-dark-card rounded-xl p-6 border border-dark-border">
        <h2 class="text-xl font-bold text-white mb-4">Recent Activities</h2>
        <div class="space-y-3">
            @forelse($recentActivities ?? [] as $activity)
            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-primary-blue rounded-full mt-1.5"></div>
                <div class="flex-1">
                    <p class="text-gray-300 text-sm">{{ $activity->description }}</p>
                    <p class="text-gray-500 text-xs">{{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-sm">No recent activities</p>
            @endforelse
        </div>
    </div>
</div>

@if($topProducts && count($topProducts) > 0)
<div class="mt-6 bg-dark-card rounded-xl p-6 border border-dark-border">
    <h2 class="text-xl font-bold text-white mb-4">Top Selling Products</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-400 text-sm border-b border-dark-border">
                    <th class="pb-3">Product</th>
                    <th class="pb-3">Sales</th>
                    <th class="pb-3">Quantity</th>
                    <th class="pb-3">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $product)
                <tr class="border-b border-dark-border">
                    <td class="py-3 text-white">{{ Str::limit($product->name, 40) }}</td>
                    <td class="py-3 text-gray-300">{{ $product->sales_count }}</td>
                    <td class="py-3 text-gray-300">{{ $product->total_quantity }}</td>
                    <td class="py-3 text-primary-blue font-semibold">${{ number_format($product->revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Store chart instances globally to prevent memory leaks
let revenueChart = null;
let categoriesChart = null;

// Function to destroy existing charts
function destroyCharts() {
    if (revenueChart) {
        revenueChart.destroy();
        revenueChart = null;
    }
    if (categoriesChart) {
        categoriesChart.destroy();
        categoriesChart = null;
    }
}

// Initialize charts on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Destroy existing charts first
    destroyCharts();

    // Revenue Chart Configuration
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        revenueChart = new Chart(revenueCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueLabels ?? []) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($revenueData ?? []) !!},
                    borderColor: '#45F882',
                    backgroundColor: 'rgba(69, 248, 130, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#45F882',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#9CA3AF',
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#9CA3AF',
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    }

    // Categories Chart Configuration
    const categoriesCtx = document.getElementById('categoriesChart');
    if (categoriesCtx) {
        categoriesChart = new Chart(categoriesCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryLabels ?? []) !!},
                datasets: [{
                    data: {!! json_encode($categoryData ?? []) !!},
                    backgroundColor: [
                        '#3B82F6',
                        '#8B5CF6',
                        '#10B981',
                        '#F59E0B'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': $' + context.parsed.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
}

// Handle period change
document.getElementById('revenue-period').addEventListener('change', function() {
    // Show loading state
    const button = this;
    button.disabled = true;

    // Redirect with new period parameter
    window.location.href = '{{ route("admin.dashboard") }}?period=' + this.value;
});

// Cleanup charts before page unload
window.addEventListener('beforeunload', function() {
    destroyCharts();
});

// Auto-refresh dashboard data every 5 minutes (optional)
let refreshInterval = null;

function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        // Only refresh if page is visible
        if (!document.hidden) {
            location.reload();
        }
    }, 300000); // 5 minutes
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
        refreshInterval = null;
    }
}

// Handle visibility change
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        stopAutoRefresh();
    } else {
        startAutoRefresh();
    }
});

// Start auto-refresh when page loads
startAutoRefresh();
</script>
@endpush

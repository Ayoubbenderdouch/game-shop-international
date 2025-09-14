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
            <i class="fas fa-arrow-up text-green-400 mr-1"></i>
            <span>{{ $revenueGrowth ?? '12.5' }}% from yesterday</span>
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
            <i class="fas fa-arrow-up text-green-400 mr-1"></i>
            <span>{{ $ordersGrowth ?? '8.2' }}% from yesterday</span>
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
                <option value="7">Last 7 Days</option>
                <option value="30" selected>Last 30 Days</option>
                <option value="90">Last 90 Days</option>
            </select>
        </div>
        <canvas id="revenueChart" height="100"></canvas>
    </div>

    <div class="bg-dark-card rounded-xl p-6 border border-dark-border">
        <h2 class="text-xl font-bold text-white mb-6">Top Categories</h2>
        <canvas id="categoriesChart"></canvas>
        <div class="mt-4 space-y-2">
            @foreach($topCategories ?? [] as $category)
            <div class="flex items-center justify-between">
                <span class="text-gray-400 text-sm">{{ $category->name }}</span>
                <span class="text-white font-semibold">${{ number_format($category->revenue ?? 0, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-dark-card rounded-xl p-6 border border-dark-border">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">Recent Orders</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-primary-blue hover:text-green-400 transition-all text-sm">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-gray-400 text-sm border-b border-dark-border">
                        <th class="pb-3">Order ID</th>
                        <th class="pb-3">Customer</th>
                        <th class="pb-3">Amount</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach($recentOrders ?? [] as $order)
                    <tr class="border-b border-dark-border/50">
                        <td class="py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-primary-blue hover:text-green-400">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td class="py-3">{{ $order->user->name }}</td>
                        <td class="py-3">${{ number_format($order->total_amount, 2) }}</td>
                        <td class="py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($order->status === 'completed') bg-green-900/50 text-green-400
                                @elseif($order->status === 'pending') bg-yellow-900/50 text-yellow-400
                                @elseif($order->status === 'processing') bg-blue-900/50 text-blue-400
                                @else bg-red-900/50 text-red-400
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-dark-card rounded-xl p-6 border border-dark-border">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-white">Best Selling Products</h2>
            <a href="{{ route('admin.products.index') }}" class="text-primary-blue hover:text-green-400 transition-all text-sm">View All</a>
        </div>
        <div class="space-y-4">
            @foreach($bestSellingProducts ?? [] as $product)
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-dark-bg rounded-lg overflow-hidden flex-shrink-0">
                    @if($product->image)
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-xl">🎮</div>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="text-white font-medium">{{ Str::limit($product->name, 30) }}</h3>
                    <p class="text-gray-400 text-sm">{{ $product->sales_count }} sales</p>
                </div>
                <div class="text-right">
                    <p class="text-white font-semibold">${{ number_format($product->selling_price, 2) }}</p>
                    <p class="text-gray-400 text-xs">{{ number_format($product->margin_percentage, 1) }}% margin</p>
                </div>
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
                <span class="px-2 py-1 text-xs rounded-full bg-green-900/50 text-green-400">Connected</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-400">Last Sync</span>
                <span class="text-gray-300 text-sm">{{ $lastSync ?? '2 hours ago' }}</span>
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
            @foreach($recentActivities ?? [] as $activity)
            <div class="flex items-start space-x-3">
                <div class="w-2 h-2 bg-primary-blue rounded-full mt-1.5"></div>
                <div class="flex-1">
                    <p class="text-gray-300 text-sm">{{ $activity->description }}</p>
                    <p class="text-gray-500 text-xs">{{ $activity->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($revenueLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($revenueData ?? [1200, 1900, 3000, 5000, 2000, 3000, 4500]) !!},
            borderColor: '#45F882',
            backgroundColor: 'rgba(69, 248, 130, 0.1)',
            tension: 0.4,
            fill: true
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
                    color: '#9CA3AF',
                    callback: function(value) {
                        return '$' + value;
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#9CA3AF'
                }
            }
        }
    }
});

const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
const categoriesChart = new Chart(categoriesCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoryLabels ?? ['Game Cards', 'Gift Cards', 'Subscriptions', 'Top Up']) !!},
        datasets: [{
            data: {!! json_encode($categoryData ?? [35, 25, 25, 15]) !!},
            backgroundColor: [
                '#3B82F6',
                '#8B5CF6',
                '#10B981',
                '#F59E0B'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

document.getElementById('revenue-period').addEventListener('change', function() {
    window.location.href = '{{ route("admin.dashboard") }}?period=' + this.value;
});
</script>
@endpush
@endsection

@extends('admin.layout')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-white mb-2">Reports & Analytics</h1>
    <p class="text-gray-400">View detailed insights and performance metrics</p>
</div>

<div class="flex flex-wrap gap-3 mb-6">
    <select id="period-filter" class="px-4 py-2 bg-dark-card border border-dark-border rounded-lg text-gray-300">
        <option value="7">Last 7 Days</option>
        <option value="30" selected>Last 30 Days</option>
        <option value="90">Last 90 Days</option>
        <option value="365">Last Year</option>
        <option value="custom">Custom Range</option>
    </select>

    <div id="custom-range" class="hidden flex gap-2">
        <input type="date" id="date-from" class="px-4 py-2 bg-dark-card border border-dark-border rounded-lg text-gray-300">
        <input type="date" id="date-to" class="px-4 py-2 bg-dark-card border border-dark-border rounded-lg text-gray-300">
    </div>

    <button onclick="exportReport()" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all">
        <i class="fas fa-download mr-2"></i>Export Report
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Total Revenue</span>
            <i class="fas fa-dollar-sign text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">${{ number_format($totalRevenue ?? 0, 2) }}</p>
        <div class="flex items-center mt-2 text-white/80 text-xs">
            <i class="fas fa-arrow-up text-green-400 mr-1"></i>
            <span>{{ $revenueGrowth ?? '+12.5' }}% from last period</span>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Total Orders</span>
            <i class="fas fa-shopping-cart text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $totalOrders ?? 0 }}</p>
        <div class="flex items-center mt-2 text-white/80 text-xs">
            <i class="fas fa-arrow-up text-green-400 mr-1"></i>
            <span>{{ $ordersGrowth ?? '+8.3' }}% from last period</span>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Avg Order Value</span>
            <i class="fas fa-chart-line text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">${{ number_format($avgOrderValue ?? 0, 2) }}</p>
        <div class="flex items-center mt-2 text-white/80 text-xs">
            <i class="fas fa-arrow-down text-red-400 mr-1"></i>
            <span>{{ $aovChange ?? '-2.1' }}% from last period</span>
        </div>
    </div>

    <div class="bg-gradient-to-br from-orange-600 to-orange-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Conversion Rate</span>
            <i class="fas fa-percentage text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $conversionRate ?? '3.2' }}%</p>
        <div class="flex items-center mt-2 text-white/80 text-xs">
            <i class="fas fa-arrow-up text-green-400 mr-1"></i>
            <span>{{ $conversionChange ?? '+0.5' }}% from last period</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-dark-card rounded-xl border border-dark-border">
        <div class="p-6 border-b border-dark-border">
            <h2 class="text-lg font-semibold text-white">Revenue Overview</h2>
        </div>
        <div class="p-6">
            <canvas id="revenueChart" height="150"></canvas>
        </div>
    </div>

    <div class="bg-dark-card rounded-xl border border-dark-border">
        <div class="p-6 border-b border-dark-border">
            <h2 class="text-lg font-semibold text-white">Orders Overview</h2>
        </div>
        <div class="p-6">
            <canvas id="ordersChart" height="150"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-dark-card rounded-xl border border-dark-border">
        <div class="p-6 border-b border-dark-border">
            <h2 class="text-lg font-semibold text-white">Top Products</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($topProducts ?? [] as $product)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-500 text-sm w-6">{{ $loop->iteration }}.</span>
                        <div>
                            <p class="text-white text-sm">{{ Str::limit($product->name, 30) }}</p>
                            <p class="text-gray-500 text-xs">{{ $product->sales_count }} sales</p>
                        </div>
                    </div>
                    <span class="text-primary-blue font-semibold">${{ number_format($product->revenue, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-dark-card rounded-xl border border-dark-border">
        <div class="p-6 border-b border-dark-border">
            <h2 class="text-lg font-semibold text-white">Top Categories</h2>
        </div>
        <div class="p-6">
            <canvas id="categoriesChart" height="200"></canvas>
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

    <div class="bg-dark-card rounded-xl border border-dark-border">
        <div class="p-6 border-b border-dark-border">
            <h2 class="text-lg font-semibold text-white">Top Customers</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($topCustomers ?? [] as $customer)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-white text-sm">{{ $customer->name }}</p>
                        <p class="text-gray-500 text-xs">{{ $customer->orders_count }} orders</p>
                    </div>
                    <span class="text-primary-blue font-semibold">${{ number_format($customer->total_spent, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-dark-card rounded-xl border border-dark-border">
        <div class="p-6 border-b border-dark-border">
            <h2 class="text-lg font-semibold text-white">Payment Methods</h2>
        </div>
        <div class="p-6">
            <canvas id="paymentChart" height="150"></canvas>
        </div>
    </div>

    <div class="bg-dark-card rounded-xl border border-dark-border">
        <div class="p-6 border-b border-dark-border">
            <h2 class="text-lg font-semibold text-white">Customer Geography</h2>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($topCountries ?? [] as $country)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-400 text-sm">{{ $country->name }}</span>
                        <span class="text-white text-sm">{{ $country->percentage }}%</span>
                    </div>
                    <div class="w-full bg-dark-bg rounded-full h-2">
                        <div class="bg-primary-blue h-2 rounded-full" style="width: {{ $country->percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($revenueLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($revenueData ?? [12000, 19000, 30000, 50000, 20000, 30000]) !!},
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
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                ticks: {
                    color: '#9CA3AF',
                    callback: function(value) {
                        return '$' + (value/1000) + 'k';
                    }
                }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#9CA3AF' }
            }
        }
    }
});

const ordersCtx = document.getElementById('ordersChart').getContext('2d');
new Chart(ordersCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($ordersLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!},
        datasets: [{
            label: 'Orders',
            data: {!! json_encode($ordersData ?? [65, 59, 80, 81, 56, 55, 40]) !!},
            backgroundColor: '#8B5CF6'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                ticks: { color: '#9CA3AF' }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#9CA3AF' }
            }
        }
    }
});

const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
new Chart(categoriesCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoryLabels ?? ['Games', 'Gift Cards', 'Subscriptions', 'Top Up']) !!},
        datasets: [{
            data: {!! json_encode($categoryData ?? [35, 25, 25, 15]) !!},
            backgroundColor: ['#3B82F6', '#8B5CF6', '#10B981', '#F59E0B']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        }
    }
});

const paymentCtx = document.getElementById('paymentChart').getContext('2d');
new Chart(paymentCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($paymentLabels ?? ['Stripe', 'PayPal', 'Credit Card', 'Crypto']) !!},
        datasets: [{
            data: {!! json_encode($paymentData ?? [40, 30, 20, 10]) !!},
            backgroundColor: ['#6366F1', '#06B6D4', '#EC4899', '#F59E0B']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: { color: '#9CA3AF' }
            }
        }
    }
});

document.getElementById('period-filter').addEventListener('change', function() {
    if (this.value === 'custom') {
        document.getElementById('custom-range').classList.remove('hidden');
    } else {
        document.getElementById('custom-range').classList.add('hidden');
        loadReportData(this.value);
    }
});

function loadReportData(period) {
    window.location.href = '{{ route("admin.reports.index") }}?period=' + period;
}

function exportReport() {
    const period = document.getElementById('period-filter').value;
    const params = new URLSearchParams();
    params.append('period', period);

    if (period === 'custom') {
        params.append('from', document.getElementById('date-from').value);
        params.append('to', document.getElementById('date-to').value);
    }

    window.location.href = '/admin/reports/export?' + params.toString();
}
</script>
@endpush
@endsection

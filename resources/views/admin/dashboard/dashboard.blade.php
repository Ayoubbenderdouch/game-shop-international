@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-2xl p-6 text-white">
        <h1 class="text-2xl font-bold mb-2">Welcome back, {{ auth()->user()->name }}!</h1>
        <p class="opacity-90">Here's what's happening with your store today.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Sales -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-green-500 text-sm font-semibold">+12.5%</span>
            </div>
            <div class="text-2xl font-bold mb-1">${{ number_format($stats['total_sales'] ?? 0, 2) }}</div>
            <div class="text-gray-400 text-sm">Total Sales</div>
        </div>

        <!-- Total Orders -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <span class="text-blue-500 text-sm font-semibold">+8.2%</span>
            </div>
            <div class="text-2xl font-bold mb-1">{{ number_format($stats['total_orders'] ?? 0) }}</div>
            <div class="text-gray-400 text-sm">Total Orders</div>
        </div>

        <!-- Total Customers -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <span class="text-purple-500 text-sm font-semibold">+15.3%</span>
            </div>
            <div class="text-2xl font-bold mb-1">{{ number_format($stats['total_customers'] ?? 0) }}</div>
            <div class="text-gray-400 text-sm">Total Customers</div>
        </div>

        <!-- Active Products -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <span class="text-yellow-500 text-sm font-semibold">{{ $stats['total_products'] ?? 0 }}</span>
            </div>
            <div class="text-2xl font-bold mb-1">{{ number_format($stats['total_products'] ?? 0) }}</div>
            <div class="text-gray-400 text-sm">Active Products</div>
        </div>
    </div>

    <!-- Today's Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="font-semibold mb-4 text-gray-300">Today's Performance</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Sales</span>
                    <span class="font-semibold text-green-400">${{ number_format($stats['today_sales'] ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Orders</span>
                    <span class="font-semibold">{{ $stats['today_orders'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">New Customers</span>
                    <span class="font-semibold">{{ $stats['new_customers'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Pending Orders</span>
                    <span class="font-semibold text-yellow-400">{{ $stats['pending_orders'] ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- API Balance -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="font-semibold mb-4 text-gray-300">API Status</h3>
            @if($apiBalance)
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Balance</span>
                        <span class="font-semibold text-green-400">${{ number_format($apiBalance['balance'] ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Status</span>
                        <span class="inline-block px-2 py-1 bg-green-500/20 text-green-500 text-xs rounded-full">Active</span>
                    </div>
                    <div class="mt-4">
                        <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500" style="width: {{ min(100, ($apiBalance['balance'] ?? 0) / 1000 * 100) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">API Balance Health</p>
                    </div>
                </div>
            @else
                <div class="text-center py-4">
                    <span class="text-gray-400">Unable to fetch API balance</span>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="bg-gray-800 rounded-xl p-6 border border-gray-700">
            <h3 class="font-semibold mb-4 text-gray-300">Quick Actions</h3>
            <div class="space-y-2">
                <a href="/admin/products/create" class="block w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-center transition">
                    Add New Product
                </a>
                <a href="/admin/api-sync" class="block w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg text-center transition">
                    Sync with API
                </a>
                <a href="/admin/reports" class="block w-full px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-center transition">
                    View Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-300">Recent Orders</h3>
                    <a href="/admin/orders" class="text-sm text-blue-400 hover:text-blue-300">View All</a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @forelse($recentOrders ?? [] as $order)
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="px-4 py-3 text-sm">{{ $order->order_number }}</td>
                            <td class="px-4 py-3 text-sm">{{ $order->user->name }}</td>
                            <td class="px-4 py-3 text-sm">${{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-2 py-1 text-xs rounded-full
                                    @if($order->status == 'completed') bg-green-500/20 text-green-500
                                    @elseif($order->status == 'processing') bg-blue-500/20 text-blue-500
                                    @elseif($order->status == 'pending') bg-yellow-500/20 text-yellow-500
                                    @else bg-red-500/20 text-red-500
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">No recent orders</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-300">Top Selling Products</h3>
                    <a href="/admin/reports/products" class="text-sm text-blue-400 hover:text-blue-300">View Report</a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($topProducts ?? [] as $product)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center">
                                <span class="text-xs font-bold">{{ $loop->iteration }}</span>
                            </div>
                            <div>
                                <div class="font-medium">{{ Str::limit($product->name, 30) }}</div>
                                <div class="text-sm text-gray-400">{{ $product->sold_count ?? 0 }} sold</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold">${{ number_format($product->selling_price, 2) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400">No sales data available</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    @if(($lowStockProducts ?? collect())->isNotEmpty())
    <div class="bg-red-900/20 border border-red-500/50 rounded-xl p-6">
        <h3 class="font-semibold text-red-400 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            Low Stock Alert
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($lowStockProducts as $product)
            <div class="bg-gray-800/50 rounded-lg p-3">
                <div class="font-medium mb-1">{{ Str::limit($product->name, 25) }}</div>
                <div class="text-sm text-gray-400">Stock: <span class="text-red-400 font-semibold">{{ $product->stock_quantity }}</span></div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

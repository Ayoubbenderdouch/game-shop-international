@extends('admin.layout')

@section('title', 'Orders Management')
@section('page-title', 'Orders')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-white mb-2">Orders Management</h1>
        <p class="text-gray-400">View and manage customer orders</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="exportOrders()" class="px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all">
            <i class="fas fa-download mr-2"></i>Export
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Total Orders</span>
            <i class="fas fa-shopping-cart text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $totalOrders ?? 0 }}</p>
    </div>

    <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Pending</span>
            <i class="fas fa-clock text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $pendingOrders ?? 0 }}</p>
    </div>

    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Completed</span>
            <i class="fas fa-check-circle text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $completedOrders ?? 0 }}</p>
    </div>

    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Total Revenue</span>
            <i class="fas fa-dollar-sign text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">${{ number_format($totalRevenue ?? 0, 2) }}</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-dark-card rounded-xl border border-dark-border p-4 mb-6">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders..."
                   class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
        </div>

        <div>
            <select name="status" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                <option value="all">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>

        <div>
            <select name="payment_status" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                <option value="all">All Payment</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Payment Pending</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
            </select>
        </div>

        <div>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
        </div>

        <div class="flex space-x-2">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="flex-1 px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
            <button type="submit" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>

<!-- Orders Table -->
<div class="bg-dark-card rounded-xl border border-dark-border">
    <div class="p-6 border-b border-dark-border">
        <h2 class="text-lg font-semibold text-white">All Orders</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark-bg border-b border-dark-border">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-dark-border">
                @forelse($orders ?? [] as $order)
                <tr class="hover:bg-dark-bg transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-white font-medium">{{ $order->order_number }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <p class="text-white">{{ $order->user->name ?? 'Guest' }}</p>
                            <p class="text-gray-400 text-sm">{{ $order->user->email ?? '' }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-300">
                        {{ $order->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-white font-semibold">${{ number_format($order->total_amount, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-900/50 text-yellow-400',
                                'processing' => 'bg-blue-900/50 text-blue-400',
                                'completed' => 'bg-green-900/50 text-green-400',
                                'cancelled' => 'bg-red-900/50 text-red-400',
                                'refunded' => 'bg-purple-900/50 text-purple-400'
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-900/50 text-gray-400' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $paymentColors = [
                                'pending' => 'bg-yellow-900/50 text-yellow-400',
                                'paid' => 'bg-green-900/50 text-green-400',
                                'failed' => 'bg-red-900/50 text-red-400',
                                'refunded' => 'bg-purple-900/50 text-purple-400'
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $paymentColors[$order->payment_status] ?? 'bg-gray-900/50 text-gray-400' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-400 hover:text-blue-300">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="updateOrderStatus({{ $order->id }})" class="text-green-400 hover:text-green-300">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                        No orders found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($orders) && $orders->hasPages())
    <div class="p-4 border-t border-dark-border">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<!-- Recent Orders Widget -->
<div class="mt-6 bg-dark-card rounded-xl border border-dark-border">
    <div class="p-6 border-b border-dark-border">
        <h2 class="text-lg font-semibold text-white">Recent Orders</h2>
    </div>
    <div class="p-6">
        <div class="space-y-4">
            @foreach($recentOrders ?? [] as $order)
            <div class="flex items-center justify-between p-4 bg-dark-bg rounded-lg">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-primary-blue/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-primary-blue"></i>
                    </div>
                    <div>
                        <p class="text-white font-medium">{{ $order->order_number }}</p>
                        <p class="text-gray-400 text-sm">{{ $order->user->name ?? 'Guest' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-white font-semibold">${{ number_format($order->total_amount, 2) }}</p>
                    <p class="text-gray-400 text-sm">{{ $order->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateOrderStatus(orderId) {
    // Implementation for updating order status
    console.log('Update order:', orderId);
}

function exportOrders() {
    window.location.href = '{{ route("admin.reports.export") }}?type=orders';
}
</script>
@endpush

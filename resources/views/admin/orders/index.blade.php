@extends('admin.layout')

@section('title', 'Orders Management')
@section('page-title', 'Orders')

@section('content')
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-white mb-2">Orders Management</h1>
        <p class="text-gray-400">View and manage customer orders</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <button onclick="exportOrders()" class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all">
            <i class="fas fa-download mr-2"></i>Export CSV
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Total Orders</span>
            <i class="fas fa-shopping-cart text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $totalOrders ?? 0 }}</p>
        <p class="text-white/60 text-xs mt-1">All time</p>
    </div>

    <div class="bg-gradient-to-br from-yellow-600 to-yellow-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Pending</span>
            <i class="fas fa-clock text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $pendingOrders ?? 0 }}</p>
        <p class="text-white/60 text-xs mt-1">Awaiting processing</p>
    </div>

    <div class="bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Processing</span>
            <i class="fas fa-spinner text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $processingOrders ?? 0 }}</p>
        <p class="text-white/60 text-xs mt-1">In progress</p>
    </div>

    <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-white/80 text-sm">Completed</span>
            <i class="fas fa-check-circle text-white/50"></i>
        </div>
        <p class="text-2xl font-bold text-white">{{ $completedOrders ?? 0 }}</p>
        <p class="text-white/60 text-xs mt-1">This month</p>
    </div>
</div>

<div class="bg-dark-card rounded-xl border border-dark-border">
    <div class="p-6 border-b border-dark-border">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" id="search" placeholder="Search by order #, customer..." class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
            </div>
            <div>
                <select id="status-filter" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="refunded">Refunded</option>
                </select>
            </div>
            <div>
                <input type="date" id="date-from" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
            </div>
            <div>
                <input type="date" id="date-to" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-gray-400 text-sm border-b border-dark-border">
                    <th class="px-6 pb-3">Order ID</th>
                    <th class="px-6 pb-3">Customer</th>
                    <th class="px-6 pb-3">Items</th>
                    <th class="px-6 pb-3">Total</th>
                    <th class="px-6 pb-3">Payment</th>
                    <th class="px-6 pb-3">Status</th>
                    <th class="px-6 pb-3">Date</th>
                    <th class="px-6 pb-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-300">
                @foreach($orders ?? [] as $order)
                <tr class="border-b border-dark-border/50 hover:bg-dark-bg/50 transition-all">
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-white">#{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->id }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-white">{{ $order->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-yellow-400">{{ $order->items->count() }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-semibold text-white">${{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-xs text-gray-500">{{ $order->currency }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($order->payment_status === 'paid') bg-green-900/50 text-green-400
                            @elseif($order->payment_status === 'pending') bg-yellow-900/50 text-yellow-400
                            @else bg-red-900/50 text-red-400
                            @endif">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($order->status === 'completed') bg-green-900/50 text-green-400
                            @elseif($order->status === 'pending') bg-yellow-900/50 text-yellow-400
                            @elseif($order->status === 'processing') bg-blue-900/50 text-blue-400
                            @elseif($order->status === 'cancelled') bg-gray-900/50 text-gray-400
                            @else bg-red-900/50 text-red-400
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="text-sm">{{ $order->created_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-400 hover:text-blue-300">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="updateOrderStatus({{ $order->id }})" class="text-green-400 hover:text-green-300">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="printInvoice({{ $order->id }})" class="text-purple-400 hover:text-purple-300">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(isset($orders) && $orders->hasPages())
    <div class="p-6 border-t border-dark-border">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<div id="status-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeStatusModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-dark-card rounded-xl border border-dark-border">
            <div class="p-6 border-b border-dark-border">
                <h2 class="text-xl font-bold text-white">Update Order Status</h2>
            </div>
            <form id="status-form" class="p-6">
                @csrf
                @method('PATCH')
                <input type="hidden" id="order-id" name="order_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Order Status</label>
                        <select id="order-status" name="status" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Payment Status</label>
                        <select id="payment-status" name="payment_status" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2 bg-dark-bg border border-dark-border rounded-lg text-gray-300 focus:border-primary-blue focus:outline-none"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end space-x-3">
                        <button type="button" onclick="closeStatusModal()" class="px-4 py-2 bg-dark-bg border border-dark-border text-gray-400 rounded-lg hover:bg-gray-800 transition-all">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary-blue text-black font-semibold rounded-lg hover:bg-green-400 transition-all">
                            Update Status
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateOrderStatus(orderId) {
    document.getElementById('order-id').value = orderId;
    document.getElementById('status-modal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('status-modal').classList.add('hidden');
}

document.getElementById('status-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const orderId = document.getElementById('order-id').value;
    const formData = new FormData(this);

    fetch(`/admin/orders/${orderId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        }
    });
});

function printInvoice(orderId) {
    window.open(`/admin/orders/${orderId}/invoice`, '_blank');
}

function exportOrders() {
    const params = new URLSearchParams();
    const search = document.getElementById('search').value;
    const status = document.getElementById('status-filter').value;
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;

    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);

    window.location.href = `/admin/orders/export?${params.toString()}`;
}

document.getElementById('search').addEventListener('input', debounce(filterOrders, 500));
document.getElementById('status-filter').addEventListener('change', filterOrders);
document.getElementById('date-from').addEventListener('change', filterOrders);
document.getElementById('date-to').addEventListener('change', filterOrders);

function filterOrders() {
    const params = new URLSearchParams();
    const search = document.getElementById('search').value;
    const status = document.getElementById('status-filter').value;
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;

    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);

    window.location.href = '{{ route("admin.orders.index") }}?' + params.toString();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush

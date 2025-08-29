@extends('layouts.app')

@section('title', __('app.freefire.orders_title'))

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2 text-orange-400">{{ __('app.freefire.my_diamond_orders') }}</h1>
        <p class="text-gray-400">{{ __('app.freefire.orders_subtitle') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-sm">{{ __('app.freefire.total_orders') }}</span>
                <svg class="w-5 h-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 002 0h6a1 1 0 100 2 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-white">{{ $orders->total() }}</p>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-sm">{{ __('app.freefire.total_diamonds_purchased') }}</span>
                <svg class="w-5 h-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-orange-400">
                {{ number_format($orders->where('status', 'completed')->sum('diamond_amount')) }} 💎
            </p>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-sm">{{ __('app.freefire.total_spent') }}</span>
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-green-400">
                ${{ number_format($orders->where('status', 'completed')->sum('price'), 2) }}
            </p>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-sm">{{ __('app.freefire.success_rate') }}</span>
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-green-400">
                @if($orders->count() > 0)
                    {{ round(($orders->where('status', 'completed')->count() / $orders->count()) * 100) }}%
                @else
                    0%
                @endif
            </p>
        </div>
    </div>

    <!-- Orders List -->
    @if($orders->isEmpty())
        <div class="text-center py-16 bg-gray-800 rounded-lg">
            <div class="mb-4">
                <svg class="w-24 h-24 mx-auto text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 002 0h6a1 1 0 100 2 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-xl text-gray-400 mb-4">{{ __('app.freefire.no_orders_yet') }}</p>
            <p class="text-gray-500 mb-6">{{ __('app.freefire.no_orders_description') }}</p>
            <a href="/freefire" class="neon-button-orange px-8 py-3 rounded-lg inline-block font-semibold">
                {{ __('app.freefire.buy_diamonds_now') }}
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-gray-800 rounded-lg overflow-hidden border border-gray-700 hover:border-orange-500/50 transition-all duration-300">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Order Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="font-semibold text-lg">{{ __('app.freefire.order_number', ['number' => str_pad($order->id, 6, '0', STR_PAD_LEFT)]) }}</h3>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                    @if($order->status == 'completed') bg-green-500/20 text-green-400 border border-green-500/30
                                    @elseif($order->status == 'processing') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                                    @elseif($order->status == 'pending') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                    @elseif($order->status == 'failed') bg-red-500/20 text-red-400 border border-red-500/30
                                    @endif">
                                    {{ __('app.freefire.status_' . $order->status) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500 mb-1">{{ __('app.freefire.player_id') }}</p>
                                    <p class="font-mono font-semibold text-orange-400">{{ $order->player_id }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 mb-1">{{ __('app.freefire.diamond_amount') }}</p>
                                    <p class="font-semibold text-lg">{{ number_format($order->diamond_amount) }} 💎</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 mb-1">{{ __('app.freefire.price') }}</p>
                                    <p class="font-semibold text-lg text-green-400">${{ number_format($order->price, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 mb-1">{{ __('app.freefire.date') }}</p>
                                    <p class="font-semibold">{{ $order->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</p>
                                </div>
                            </div>

                            @if($order->redemption_code && $order->status == 'completed')
                            <div class="mt-4 p-3 bg-orange-900/20 border border-orange-500/30 rounded-lg">
                                <p class="text-xs text-orange-400 mb-1">{{ __('app.freefire.redemption_code') }}:</p>
                                <p class="font-mono text-sm font-bold text-orange-400">{{ $order->redemption_code }}</p>
                            </div>
                            @endif

                            @if($order->transaction_id)
                            <div class="mt-4 p-3 bg-gray-900 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">{{ __('app.freefire.transaction_id') }}</p>
                                <p class="font-mono text-sm">{{ $order->transaction_id }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col gap-2">
                            @if($order->status == 'completed')
                                <button onclick="showReceipt('{{ $order->id }}')"
                                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-sm transition flex items-center gap-2 justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('app.freefire.download_receipt') }}
                                </button>
                            @endif

                            <button onclick="toggleDetails('{{ $order->id }}')"
                                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-sm transition">
                                {{ __('app.freefire.view_details') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</div>

<script>
function toggleDetails(orderId) {
    // Implementation for expanding order details
    console.log('Toggle details for order:', orderId);
}

function showReceipt(orderId) {
    // Implementation for showing receipt
    console.log('Show receipt for order:', orderId);
}
</script>
@endsection

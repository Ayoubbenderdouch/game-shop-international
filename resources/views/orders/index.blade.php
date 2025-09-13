@extends('layouts.app')

@section('title', __('orders.title'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900/20 to-slate-900">
    <div class="container mx-auto px-4 pt-24 pb-16">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-black text-white mb-2">{{ __('orders.title') ?: 'My Orders' }}</h1>
            <p class="text-slate-400">{{ __('orders.subtitle') ?: 'View and manage your order history' }}</p>
        </div>

        @if($orders->count() > 0)
        <!-- Orders List -->
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <!-- Order Info -->
                    <div class="flex-1">
                        <div class="flex items-center gap-4 mb-2">
                            <h3 class="text-xl font-bold text-white">
                                Order #{{ $order->order_number ?? $order->id }}
                            </h3>
                            @if($order->status === 'completed')
                                <span class="px-3 py-1 bg-green-500/20 text-green-400 text-xs font-medium rounded-full">
                                    {{ ucfirst($order->status) }}
                                </span>
                            @elseif($order->status === 'processing')
                                <span class="px-3 py-1 bg-blue-500/20 text-blue-400 text-xs font-medium rounded-full">
                                    {{ ucfirst($order->status) }}
                                </span>
                            @elseif($order->status === 'pending')
                                <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 text-xs font-medium rounded-full">
                                    {{ ucfirst($order->status) }}
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-500/20 text-red-400 text-xs font-medium rounded-full">
                                    {{ ucfirst($order->status) }}
                                </span>
                            @endif
                        </div>

                        <div class="text-sm text-slate-400 mb-3">
                            {{ $order->created_at->format('F j, Y - g:i A') }}
                        </div>

                        <!-- Order Items Preview -->
                        <div class="space-y-2">
                            @foreach($order->orderItems->take(2) as $item)
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-slate-900 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ $item->product->image }}" alt="{{ $item->product_name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-600">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-white font-medium">{{ $item->product_name }}</p>
                                    <p class="text-xs text-slate-400">Qty: {{ $item->quantity }} × ${{ number_format($item->selling_price, 2) }}</p>
                                </div>
                            </div>
                            @endforeach

                            @if($order->orderItems->count() > 2)
                            <p class="text-xs text-slate-400 italic">
                                +{{ $order->orderItems->count() - 2 }} more items
                            </p>
                            @endif
                        </div>
                    </div>

                    <!-- Order Actions & Total -->
                    <div class="lg:text-right space-y-4">
                        <div>
                            <p class="text-sm text-slate-400 mb-1">Total Amount</p>
                            <p class="text-2xl font-bold text-cyan-400">
                                ${{ number_format($order->total_amount ?? $order->total ?? 0, 2) }}
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row lg:flex-col gap-2">
                            <a href="{{ route('orders.show', $order) }}"
                               class="px-4 py-2 bg-cyan-500 text-white font-medium rounded-lg hover:bg-cyan-600 transition-colors text-center">
                                View Details
                            </a>

                            @if($order->status === 'completed')
                            <a href="{{ route('orders.invoice', $order) }}"
                               class="px-4 py-2 bg-slate-700 text-white font-medium rounded-lg hover:bg-slate-600 transition-colors text-center">
                                Download Invoice
                            </a>
                            @endif

                            @if($order->status === 'pending')
                            <form action="{{ route('orders.cancel', $order) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                @csrf
                                <button type="submit"
                                        class="w-full px-4 py-2 bg-red-500/20 text-red-400 font-medium rounded-lg hover:bg-red-500/30 transition-colors">
                                    Cancel Order
                                </button>
                            </form>
                            @endif
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
        @else
        <!-- Empty State -->
        <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-6 bg-slate-900 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-slate-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">No orders yet</h3>
            <p class="text-slate-400 mb-6">Start shopping to see your orders here</p>
            <a href="{{ route('shop') }}"
               class="inline-block px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-xl hover:from-cyan-600 hover:to-blue-600 transition-all">
                Start Shopping
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

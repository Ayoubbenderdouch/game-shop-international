@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900/20 to-slate-900">
    <div class="container mx-auto px-4 pt-24 pb-16">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('orders.index') }}"
                   class="p-2 bg-slate-800 text-slate-400 rounded-lg hover:bg-slate-700 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-4xl font-black text-white">Order Details</h1>
            </div>
            <p class="text-slate-400">Order #{{ $order->order_number ?? str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Status -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6">
                    <h2 class="text-xl font-bold text-white mb-4">Order Status</h2>

                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm text-slate-400 mb-1">Current Status</p>
                            @if($order->status === 'completed')
                                <span class="inline-block px-4 py-2 bg-green-500/20 text-green-400 font-medium rounded-lg">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Completed
                                </span>
                            @elseif($order->status === 'processing')
                                <span class="inline-block px-4 py-2 bg-blue-500/20 text-blue-400 font-medium rounded-lg">
                                    <svg class="w-5 h-5 inline-block mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing
                                </span>
                            @elseif($order->status === 'pending')
                                <span class="inline-block px-4 py-2 bg-yellow-500/20 text-yellow-400 font-medium rounded-lg">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Pending
                                </span>
                            @else
                                <span class="inline-block px-4 py-2 bg-red-500/20 text-red-400 font-medium rounded-lg">
                                    {{ ucfirst($order->status) }}
                                </span>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-slate-400 mb-1">Order Date</p>
                            <p class="text-white">{{ $order->created_at->format('F j, Y') }}</p>
                            <p class="text-xs text-slate-500">{{ $order->created_at->format('g:i A') }}</p>
                        </div>
                    </div>

                    <!-- Status Timeline -->
                    <div class="border-t border-slate-700 pt-4">
                        <div class="relative">
                            <div class="absolute left-4 top-8 bottom-0 w-0.5 bg-slate-700"></div>

                            <div class="space-y-4">
                                <!-- Order Placed -->
                                <div class="flex items-start gap-4">
                                    <div class="relative z-10 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-white font-medium">Order Placed</p>
                                        <p class="text-sm text-slate-400">{{ $order->created_at->format('M j, Y - g:i A') }}</p>
                                    </div>
                                </div>

                                <!-- Payment Confirmed -->
                                @if($order->payment_status === 'paid')
                                <div class="flex items-start gap-4">
                                    <div class="relative z-10 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-white font-medium">Payment Confirmed</p>
                                        <p class="text-sm text-slate-400">{{ $order->updated_at->format('M j, Y - g:i A') }}</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Processing/Completed -->
                                @if($order->status === 'completed')
                                <div class="flex items-start gap-4">
                                    <div class="relative z-10 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-white font-medium">Order Completed</p>
                                        <p class="text-sm text-slate-400">{{ $order->updated_at->format('M j, Y - g:i A') }}</p>
                                    </div>
                                </div>
                                @elseif($order->status === 'processing')
                                <div class="flex items-start gap-4">
                                    <div class="relative z-10 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center animate-pulse">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-white font-medium">Processing Order</p>
                                        <p class="text-sm text-slate-400">In progress...</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6">
                    <h2 class="text-xl font-bold text-white mb-4">Order Items</h2>

                    <div class="space-y-4">
                        @foreach($order->orderItems as $item)
                        <div class="flex items-start gap-4 pb-4 border-b border-slate-700 last:border-0">
                            <!-- Product Image -->
                            <div class="w-20 h-20 bg-slate-900 rounded-lg overflow-hidden flex-shrink-0">
                                @if($item->product && $item->product->image)
                                    <img src="{{ $item->product->image }}" alt="{{ $item->product_name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600">
                                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1">
                                <h3 class="font-bold text-white mb-1">{{ $item->product_name }}</h3>
                                <p class="text-sm text-slate-400 mb-2">Quantity: {{ $item->quantity }}</p>

                                @if($item->optional_fields_data)
                                <div class="bg-slate-900/50 rounded-lg p-2 mb-2">
                                    <p class="text-xs text-cyan-400 mb-1">Custom Details:</p>
                                    @foreach($item->optional_fields_data as $key => $value)
                                    <p class="text-xs text-slate-400">{{ $key }}: {{ $value }}</p>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Item Status -->
                                @if($item->status === 'delivered')
                                    <span class="inline-block px-2 py-1 bg-green-500/20 text-green-400 text-xs font-medium rounded">
                                        Delivered
                                    </span>
                                @elseif($item->status === 'pending')
                                    <span class="inline-block px-2 py-1 bg-yellow-500/20 text-yellow-400 text-xs font-medium rounded">
                                        Pending
                                    </span>
                                @elseif($item->status === 'failed')
                                    <span class="inline-block px-2 py-1 bg-red-500/20 text-red-400 text-xs font-medium rounded">
                                        Failed
                                    </span>
                                @endif

                                @if($item->serial_codes)
                                <div class="mt-2 bg-green-500/10 border border-green-500/30 rounded-lg p-2">
                                    <p class="text-xs text-green-400 mb-1">Serial Codes:</p>
                                    <code class="text-xs text-white bg-slate-900 px-2 py-1 rounded">{{ $item->serial_codes }}</code>
                                </div>
                                @endif
                            </div>

                            <!-- Price -->
                            <div class="text-right">
                                <p class="text-sm text-slate-400">Unit Price</p>
                                <p class="text-white font-medium">${{ number_format($item->selling_price, 2) }}</p>
                                <p class="text-sm text-slate-400 mt-2">Total</p>
                                <p class="text-lg font-bold text-cyan-400">${{ number_format($item->total_price, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary & Actions -->
            <div class="lg:col-span-1">
                <!-- Payment Summary -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6 mb-6">
                    <h2 class="text-xl font-bold text-white mb-4">Payment Summary</h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal</span>
                            <span>${{ number_format($order->subtotal ?? 0, 2) }}</span>
                        </div>
                        @if(($order->vat_amount ?? 0) > 0)
                        <div class="flex justify-between text-slate-400">
                            <span>VAT</span>
                            <span>${{ number_format($order->vat_amount, 2) }}</span>
                        </div>
                        @endif
                        @if(($order->discount_amount ?? 0) > 0)
                        <div class="flex justify-between text-slate-400">
                            <span>Discount</span>
                            <span class="text-green-400">-${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-slate-400">
                            <span>Shipping</span>
                            <span class="text-green-400">Free</span>
                        </div>
                    </div>

                    <div class="border-t border-slate-700 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-white">Total</span>
                            <span class="text-2xl font-black text-cyan-400">
                                ${{ number_format($order->total_amount ?? $order->total ?? 0, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mt-6 pt-6 border-t border-slate-700">
                        <p class="text-sm text-slate-400 mb-2">Payment Method</p>
                        <div class="flex items-center gap-2">
                            <svg class="w-8 h-5" viewBox="0 0 32 20" fill="none">
                                <rect width="32" height="20" rx="4" fill="#1e293b"/>
                                <path d="M8 10h16M8 14h8" stroke="#475569" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span class="text-white">{{ ucfirst($order->payment_method ?? 'Credit Card') }}</span>
                        </div>

                        <div class="mt-3">
                            <p class="text-sm text-slate-400 mb-1">Payment Status</p>
                            @if($order->payment_status === 'paid')
                                <span class="inline-block px-3 py-1 bg-green-500/20 text-green-400 text-sm font-medium rounded">
                                    Paid
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 bg-yellow-500/20 text-yellow-400 text-sm font-medium rounded">
                                    {{ ucfirst($order->payment_status ?? 'Pending') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="space-y-3">
                    @if($order->status === 'completed')
                    <a href="{{ route('orders.invoice', $order) }}"
                       class="w-full px-4 py-3 bg-cyan-500 text-white font-bold rounded-xl hover:bg-cyan-600 transition-colors text-center flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Invoice
                    </a>
                    @endif

                    @if($order->status === 'pending')
                    <form action="{{ route('orders.cancel', $order) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to cancel this order?');">
                        @csrf
                        <button type="submit"
                                class="w-full px-4 py-3 bg-red-500/20 text-red-400 font-bold rounded-xl hover:bg-red-500/30 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Cancel Order
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('orders.index') }}"
                       class="w-full px-4 py-3 bg-slate-700 text-white font-bold rounded-xl hover:bg-slate-600 transition-colors text-center">
                        Back to Orders
                    </a>
                </div>

                <!-- Need Help -->
                <div class="mt-6 bg-blue-500/10 border border-blue-500/30 rounded-xl p-4">
                    <h3 class="font-bold text-white mb-2">Need Help?</h3>
                    <p class="text-sm text-slate-400 mb-3">
                        If you have any questions about your order, please contact our support team.
                    </p>
                    <a href="#" class="text-sm text-cyan-400 hover:text-cyan-300">
                        Contact Support →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

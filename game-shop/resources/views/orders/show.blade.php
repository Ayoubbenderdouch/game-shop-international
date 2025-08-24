@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Order #{{ $order->order_number }}</h1>

    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-400">Status</p>
                <p class="font-semibold">
                    <span class="inline-block px-3 py-1 rounded text-sm
                        @if($order->status == 'completed') bg-green-500/20 text-green-500
                        @elseif($order->status == 'processing') bg-yellow-500/20 text-yellow-500
                        @elseif($order->status == 'failed') bg-red-500/20 text-red-500
                        @else bg-gray-500/20 text-gray-500
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-400">Date</p>
                <p class="font-semibold">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400">Payment Method</p>
                <p class="font-semibold">{{ $order->payment_method ?? 'Card' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-400">Total</p>
                <p class="font-semibold text-[#49baee]">${{ number_format($order->total_amount, 2) }}</p>
            </div>
        </div>

        <h2 class="text-xl font-semibold mb-4">Order Items</h2>

        <div class="space-y-4">
            @foreach($order->orderItems as $item)
            <div class="border border-gray-700 rounded-lg p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold">{{ $item->product->title }}</h3>
                        <p class="text-sm text-gray-400">Quantity: {{ $item->quantity }}</p>
                        <p class="text-sm text-gray-400">Price: ${{ $item->price }} each</p>
                    </div>
                    <span class="font-semibold">${{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>

                @if($order->status == 'completed' && $item->productCode)
                <div class="mt-4 p-3 bg-green-900/20 border border-green-600 rounded">
                    <p class="text-sm text-green-400 mb-2">Product Code:</p>
                    <code class="text-lg font-mono">{{ $item->productCode->code }}</code>
                    <p class="text-xs text-gray-400 mt-2">Keep this code safe. It will not be shown again.</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex justify-between">
        <a href="/orders" class="text-gray-400 hover:text-white transition">
            ← Back to Orders
        </a>
        @if($order->status == 'completed')
        <button onclick="window.print()" class="neon-button px-6 py-2 rounded">
            Print Invoice
        </button>
        @endif
    </div>
</div>
@endsection

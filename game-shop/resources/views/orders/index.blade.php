@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">My Orders</h1>

    @if($orders->isEmpty())
        <div class="text-center py-16 bg-gray-800 rounded-lg">
            <p class="text-xl text-gray-400 mb-4">No orders yet</p>
            <a href="/shop" class="neon-button px-6 py-2 rounded inline-block">
                Start Shopping
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-gray-800 rounded-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-semibold text-lg">Order #{{ $order->order_number }}</h3>
                        <p class="text-sm text-gray-400">{{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded text-sm
                            @if($order->status == 'completed') bg-green-500/20 text-green-500
                            @elseif($order->status == 'processing') bg-yellow-500/20 text-yellow-500
                            @elseif($order->status == 'failed') bg-red-500/20 text-red-500
                            @else bg-gray-500/20 text-gray-500
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                        <p class="text-xl font-bold mt-2">${{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-700 pt-4">
                    <div class="space-y-2">
                        @foreach($order->orderItems as $item)
                        <div class="flex justify-between">
                            <span>{{ $item->product->title }} x{{ $item->quantity }}</span>
                            <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4">
                    <a href="/orders/{{ $order->id }}" class="text-[#49baee] hover:underline">
                        View Details →
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection

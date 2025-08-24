@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Admin Dashboard</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gray-800 rounded-lg p-6">
        <p class="text-sm text-gray-400 mb-2">Total Revenue</p>
        <p class="text-3xl font-bold text-[#49baee]">${{ number_format($stats['total_revenue'], 2) }}</p>
    </div>

    <div class="bg-gray-800 rounded-lg p-6">
        <p class="text-sm text-gray-400 mb-2">Total Orders</p>
        <p class="text-3xl font-bold">{{ $stats['total_orders'] }}</p>
    </div>

    <div class="bg-gray-800 rounded-lg p-6">
        <p class="text-sm text-gray-400 mb-2">Total Products</p>
        <p class="text-3xl font-bold">{{ $stats['total_products'] }}</p>
    </div>

    <div class="bg-gray-800 rounded-lg p-6">
        <p class="text-sm text-gray-400 mb-2">Total Users</p>
        <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Recent Orders</h2>

        @if($stats['recent_orders']->isEmpty())
            <p class="text-gray-400">No orders yet</p>
        @else
            <div class="space-y-3">
                @foreach($stats['recent_orders'] as $order)
                <div class="flex justify-between items-center pb-3 border-b border-gray-700 last:border-0">
                    <div>
                        <p class="font-medium">{{ $order->order_number }}</p>
                        <p class="text-sm text-gray-400">{{ $order->user->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">${{ number_format($order->total_amount, 2) }}</p>
                        <p class="text-sm text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Low Stock Products</h2>

        @if($stats['low_stock_products']->isEmpty())
            <p class="text-gray-400">All products well stocked</p>
        @else
            <div class="space-y-3">
                @foreach($stats['low_stock_products'] as $product)
                <div class="flex justify-between items-center pb-3 border-b border-gray-700 last:border-0">
                    <div>
                        <p class="font-medium">{{ $product->title }}</p>
                        <p class="text-sm text-gray-400">{{ $product->category->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-red-500">{{ $product->available_stock }} left</p>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

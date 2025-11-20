@extends('layouts.app')

@section('title', 'Dashboard - GameShop')

@section('header')
    <h1 class="text-white text-[42px] font-bold mb-4">Dashboard</h1>
    <nav class="flex items-center space-x-2 text-sm">
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-all">Home</a>
        <span class="text-gray-400">/</span>
        <span class="text-white">Dashboard</span>
    </nav>
@endsection

@section('content')
<div class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        @auth
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-primary-blue to-[#3fda74] rounded-2xl p-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-black mb-2">Welcome back, {{ Auth::user()->name }}!</h2>
                    <p class="text-black/80">Manage your account and track your orders</p>
                </div>
                @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="bg-black text-white px-6 py-3 rounded-lg font-bold hover:bg-gray-900 transition-all">
                    Admin Panel →
                </a>
                @endif
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            <!-- Wallet Balance -->
            <div class="bg-black border border-[#23262B] rounded-lg p-6 hover:border-primary-blue transition-all cursor-pointer" onclick="window.location='{{ route('wallet.index') }}'">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <span class="text-gray-400 text-sm">Wallet</span>
                </div>
                <p class="text-3xl font-bold text-white">
                    ${{ number_format(Auth::user()->wallet_balance, 2) }}
                </p>
                <p class="text-gray-400 text-sm mt-1">Available Balance</p>
            </div>
            
            <!-- Total Orders -->
            <div class="bg-black border border-[#23262B] rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-primary-blue/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <span class="text-gray-400 text-sm">Total</span>
                </div>
                <p class="text-3xl font-bold text-white">
                    @php
                        try {
                            $orderCount = Auth::user()->orders()->count();
                        } catch (\Exception $e) {
                            $orderCount = 0;
                        }
                    @endphp
                    {{ $orderCount }}
                </p>
                <p class="text-gray-400 text-sm mt-1">Orders</p>
            </div>

            <!-- Cart Items -->
            <div class="bg-black border border-[#23262B] rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <span class="text-gray-400 text-sm">In Cart</span>
                </div>
                <p class="text-3xl font-bold text-white">
                    @php
                        try {
                            $cartCount = Auth::user()->cartItems()->count();
                        } catch (\Exception $e) {
                            $cartCount = 0;
                        }
                    @endphp
                    {{ $cartCount }}
                </p>
                <p class="text-gray-400 text-sm mt-1">Items</p>
            </div>

            <!-- Favorites -->
            <div class="bg-black border border-[#23262B] rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-gray-400 text-sm">Saved</span>
                </div>
                <p class="text-3xl font-bold text-white">
                    @php
                        try {
                            $favCount = Auth::user()->favorites()->count();
                        } catch (\Exception $e) {
                            $favCount = 0;
                        }
                    @endphp
                    {{ $favCount }}
                </p>
                <p class="text-gray-400 text-sm mt-1">Favorites</p>
            </div>

            <!-- Total Spent -->
            <div class="bg-black border border-[#23262B] rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-gray-400 text-sm">Spent</span>
                </div>
                <p class="text-3xl font-bold text-white">
                    @php
                        try {
                            $totalSpent = Auth::user()->orders()->where('status', 'completed')->sum('total');
                        } catch (\Exception $e) {
                            $totalSpent = 0;
                        }
                    @endphp
                    ${{ number_format($totalSpent, 2) }}
                </p>
                <p class="text-gray-400 text-sm mt-1">Total</p>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Recent Orders -->
            <div class="bg-black border border-[#23262B] rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-white">Recent Orders</h3>
                    <a href="{{ route('orders.index') }}" class="text-primary-blue hover:text-[#3fda74] text-sm">
                        View All →
                    </a>
                </div>

                @php
                    $recentOrders = Auth::user()->orders()->latest()->take(5)->get();
                @endphp

                @if($recentOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between pb-4 border-b border-[#23262B] last:border-0">
                        <div>
                            <p class="text-white font-semibold">#{{ $order->order_number ?? $order->id }}</p>
                            <p class="text-gray-400 text-sm">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-primary-blue font-bold">${{ number_format($order->total, 2) }}</p>
                            <span class="text-xs px-2 py-1 rounded-full
                                {{ $order->status == 'completed' ? 'bg-green-500/20 text-green-400' :
                                   ($order->status == 'pending' ? 'bg-yellow-500/20 text-yellow-400' :
                                   'bg-gray-500/20 text-gray-400') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-400 text-center py-8">No orders yet</p>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-black border border-[#23262B] rounded-lg p-6">
                <h3 class="text-xl font-bold text-white mb-6">Quick Actions</h3>

                <div class="space-y-3">
                    <a href="{{ route('shop') }}" class="flex items-center justify-between p-4 bg-[#0b0e13] rounded-lg hover:bg-[#23262B] transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-blue/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold">Browse Shop</p>
                                <p class="text-gray-400 text-sm">Explore our products</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-blue transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <a href="{{ route('cart.index') }}" class="flex items-center justify-between p-4 bg-[#0b0e13] rounded-lg hover:bg-[#23262B] transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold">View Cart</p>
                                <p class="text-gray-400 text-sm">{{ $cartCount }} items in cart</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-blue transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <a href="{{ route('favorites.index') }}" class="flex items-center justify-between p-4 bg-[#0b0e13] rounded-lg hover:bg-[#23262B] transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold">My Favorites</p>
                                <p class="text-gray-400 text-sm">{{ $favCount }} saved items</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-blue transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-4 bg-[#0b0e13] rounded-lg hover:bg-[#23262B] transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-semibold">Edit Profile</p>
                                <p class="text-gray-400 text-sm">Update your information</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-primary-blue transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-400 mb-6">You are not logged in.</p>
            <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-primary-blue text-black font-bold rounded-lg hover:bg-[#3fda74] transition-all">
                Login to Continue
            </a>
        </div>
        @endauth
    </div>
</div>
@endsection

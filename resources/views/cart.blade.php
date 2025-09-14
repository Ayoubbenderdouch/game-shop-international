@extends('layouts.app')

@section('title', __('cart.title'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900/20 to-slate-900">
    <div class="container mx-auto px-4 pt-24 pb-16">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-black text-white mb-2">{{ __('cart.title') }}</h1>
            <p class="text-slate-400">{{ __('cart.manage_items') }}</p>
        </div>

        @if($cartItems->count() > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6">
                    <div class="flex items-center gap-6">
                        <!-- Product Image -->
                        <div class="w-24 h-24 bg-slate-900 rounded-xl overflow-hidden flex-shrink-0">
                            @if($item->product->image)
                                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-600">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Product Details -->
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-white mb-1">
                                <a href="{{ route('product.show', $item->product->slug) }}" class="hover:text-cyan-400 transition-colors">
                                    {{ $item->product->name }}
                                </a>
                            </h3>
                            <p class="text-sm text-cyan-400 mb-2">{{ $item->product->category->name }}</p>
                            <p class="text-2xl font-bold text-white">
                                ${{ number_format($item->product->selling_price, 2) }}
                            </p>
                        </div>

                        <!-- Quantity and Actions -->
                        <div class="flex items-center gap-4">
                            <!-- Update Quantity Form -->
                            <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item->quantity }}"
                                       min="1" @if($item->product->stock_quantity !== null) max="{{ $item->product->stock_quantity }}" @endif
                                       class="w-20 px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white text-center focus:outline-none focus:border-cyan-500"
                                       onchange="this.form.submit()">
                            </form>

                            <!-- Remove Item -->
                            <form action="{{ route('cart.remove', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-400 hover:text-red-300 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Subtotal for this item -->
                    <div class="mt-4 pt-4 border-t border-slate-700 flex justify-between items-center">
                        <span class="text-slate-400">{{ __('cart.subtotal') }}</span>
                        <span class="text-xl font-bold text-white">
                            ${{ number_format($item->product->selling_price * $item->quantity, 2) }}
                        </span>
                    </div>
                </div>
                @endforeach

                <!-- Clear Cart Button -->
                <form action="{{ route('cart.clear') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('{{ __('cart.clear_confirm') }}')"
                            class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors">
                        {{ __('cart.clear_cart') }}
                    </button>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6 sticky top-24">
                    <h2 class="text-2xl font-bold text-white mb-6">{{ __('cart.order_summary') }}</h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-slate-400">
                            <span>{{ __('cart.items_count', ['count' => $cartItems->sum('quantity')]) }}</span>
                            <span>${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>{{ __('cart.shipping') }}</span>
                            <span class="text-green-400">{{ __('cart.free') }}</span>
                        </div>
                        @if(config('services.tax.enabled'))
                        <div class="flex justify-between text-slate-400">
                            <span>{{ __('cart.tax') }}</span>
                            <span>${{ number_format($total * config('services.tax.rate'), 2) }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="border-t border-slate-700 pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-white">{{ __('cart.total') }}</span>
                            <span class="text-3xl font-black text-cyan-400">
                                ${{ number_format($total, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <a href="{{ route('checkout.index') }}"
                       class="block w-full text-center px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-xl hover:from-cyan-600 hover:to-blue-600 transition-all transform hover:scale-105">
                        {{ __('app.cart.proceed_to_checkout') }}
                    </a>

                    <!-- Continue Shopping -->
                    <a href="{{ route('shop') }}"
                       class="block w-full text-center px-6 py-3 mt-4 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                        {{ __('app.cart.continue_shopping') }}
                    </a>

                    <!-- Security Badge -->
                    <div class="mt-6 pt-6 border-t border-slate-700">
                        <div class="flex items-center gap-2 text-sm text-slate-400">
                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span>{{ __('app.cart.secure_checkout') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-slate-600 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-white mb-2">{{ __('cart.empty_cart') }}</h2>
                <p class="text-slate-400 mb-8">{{ __('cart.empty_cart_message') }}</p>
                <a href="{{ route('shop') }}"
                   class="inline-block px-8 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-xl hover:from-cyan-600 hover:to-blue-600 transition-all transform hover:scale-105">
                    {{ __('app.cart.start_shopping') }}
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

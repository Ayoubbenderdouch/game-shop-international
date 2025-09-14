@extends('layouts.app')

@section('title', 'Shopping Cart - GameShop')

@section('header')
    <h1 class="text-white text-[42px] font-bold mb-4">Shopping Cart</h1>
    <nav class="flex items-center space-x-2 text-sm">
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-all">Home</a>
        <span class="text-gray-400">/</span>
        <span class="text-white">Cart</span>
    </nav>
@endsection

@section('content')
<!-- Cart Section -->
<section class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        @if(isset($cartItems) && $cartItems->count() > 0)
        <div class="lg:flex lg:space-x-8">
            <!-- Cart Items -->
            <div class="flex-1">
                <div class="bg-black border border-[#23262B] rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-[#23262B]">
                        <h2 class="text-xl font-bold text-white">Cart Items ({{ $cartItems->count() }})</h2>
                    </div>

                    <div class="divide-y divide-[#23262B]">
                        @foreach($cartItems as $item)
                        <div class="p-6 flex items-center gap-6">
                            <!-- Product Image -->
                            <div class="w-24 h-24 bg-gradient-to-br from-[#23262B] to-black rounded-lg flex items-center justify-center flex-shrink-0">
                                @if($item->product->image)
                                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover rounded-lg">
                                @else
                                <div class="text-4xl">🎮</div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1">
                                <h3 class="text-white font-semibold mb-1">{{ $item->product->name }}</h3>
                                <p class="text-gray-400 text-sm mb-2">{{ $item->product->category->name ?? 'Digital Product' }}</p>

                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center border border-[#23262B] rounded-lg">
                                        <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                class="px-3 py-1 text-gray-400 hover:text-white transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <span class="px-4 text-white">{{ $item->quantity }}</span>
                                        <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                class="px-3 py-1 text-gray-400 hover:text-white transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <button onclick="removeFromCart({{ $item->id }})"
                                            class="text-red-400 hover:text-red-500 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="text-right">
                                @if($item->product->discount_percentage > 0)
                                <p class="text-gray-500 line-through text-sm">${{ number_format($item->product->original_price * $item->quantity, 2) }}</p>
                                @endif
                                <p class="text-primary-blue font-bold text-xl">${{ number_format($item->product->price * $item->quantity, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Coupon Code -->
                <div class="mt-6 bg-black border border-[#23262B] rounded-lg p-6">
                    <form class="flex gap-4">
                        <input type="text" placeholder="Enter coupon code"
                               class="flex-1 bg-transparent border border-[#23262B] rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:border-primary-blue focus:outline-none">
                        <button type="submit" class="px-6 py-3 bg-primary-blue text-black font-bold rounded-lg hover:bg-[#3fda74] transition-all">
                            Apply Coupon
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:w-[350px] mt-8 lg:mt-0">
                <div class="bg-black border border-[#23262B] rounded-lg p-6 sticky top-24">
                    <h3 class="text-xl font-bold text-white mb-6">Order Summary</h3>

                    <div class="space-y-4 pb-6 border-b border-[#23262B]">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Subtotal</span>
                            <span class="text-white font-semibold">
                                ${{ number_format($cartItems->sum(function($item) { return $item->product->price * $item->quantity; }), 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Discount</span>
                            <span class="text-green-400 font-semibold">
                                -${{ number_format($cartItems->sum(function($item) {
                                    return ($item->product->original_price - $item->product->price) * $item->quantity;
                                }), 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Tax</span>
                            <span class="text-white font-semibold">$0.00</span>
                        </div>
                    </div>

                    <div class="flex justify-between py-6">
                        <span class="text-white font-bold text-lg">Total</span>
                        <span class="text-primary-blue font-bold text-2xl">
                            ${{ number_format($cartItems->sum(function($item) { return $item->product->price * $item->quantity; }), 2) }}
                        </span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="block w-full text-center bg-primary-blue text-black font-bold py-4 rounded-lg hover:bg-[#3fda74] transition-all">
                        Proceed to Checkout
                    </a>

                    <a href="{{ route('shop') }}" class="block w-full text-center mt-4 text-gray-400 hover:text-white transition-all">
                        Continue Shopping
                    </a>

                    <!-- Security Badges -->
                    <div class="mt-6 pt-6 border-t border-[#23262B]">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-400">Secure Checkout</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-400">SSL Encrypted</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto mb-6 bg-black border border-[#23262B] rounded-full flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Your cart is empty</h2>
            <p class="text-gray-400 mb-8">Looks like you haven't added anything to your cart yet</p>
            <a href="{{ route('shop') }}" class="inline-block px-8 py-4 bg-primary-blue text-black font-bold rounded-lg hover:bg-[#3fda74] transition-all">
                Start Shopping
            </a>
        </div>
        @endif
    </div>
</section>
@endsection

@push('scripts')
<script>
function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(itemId);
        return;
    }

    fetch(`/cart/update/${itemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ quantity: newQuantity })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function removeFromCart(itemId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        fetch(`/cart/remove/${itemId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}
</script>
@endpush

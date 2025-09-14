@extends('layouts.app')

@section('title', 'Shopping Cart - GameShop')

@section('content')
<!-- Cart Section -->
<section class="w-full py-[60px] min-h-screen bg-gradient-to-br from-slate-900 via-purple-900/20 to-slate-900">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-black text-white mb-2">Shopping Cart</h1>
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-all">Home</a>
                <span class="text-gray-400">/</span>
                <span class="text-white">Cart</span>
            </nav>
        </div>

        @if(isset($cartItems) && $cartItems->count() > 0)
        <div class="lg:flex lg:gap-8">
            <!-- Cart Items -->
            <div class="flex-1">
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl p-6 mb-6">
                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                        <div class="flex items-center gap-4 p-4 bg-slate-900/50 rounded-lg border border-slate-700" data-item-id="{{ $item->id }}">
                            <!-- Product Image -->
                            <div class="w-24 h-24 flex-shrink-0 bg-slate-800 rounded-lg overflow-hidden">
                                @if($item->product->image)
                                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-3xl">🎮</div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1">
                                <h3 class="text-white font-semibold text-lg mb-1">{{ $item->product->name }}</h3>
                                @if($item->product->category)
                                <p class="text-gray-400 text-sm mb-2">{{ $item->product->category->name }}</p>
                                @endif

                                <!-- Price -->
                                <div class="flex items-center gap-2">
                                    @if($item->product->original_price && $item->product->original_price > $item->product->selling_price)
                                    <span class="text-gray-500 line-through text-sm">${{ number_format($item->product->original_price, 2) }}</span>
                                    @endif
                                    <span class="text-primary-blue font-bold">${{ number_format($item->product->selling_price, 2) }}</span>
                                </div>

                                @if($item->product->is_available && $item->product->stock_quantity !== null && $item->product->stock_quantity <= 5)
                                <p class="text-orange-400 text-xs mt-1">Only {{ $item->product->stock_quantity }} left in stock</p>
                                @endif
                            </div>

                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-2">
                                <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                        class="w-8 h-8 bg-slate-700 text-white rounded hover:bg-slate-600 transition-colors"
                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <input type="number" value="{{ $item->quantity }}" min="1"
                                       onchange="updateQuantity({{ $item->id }}, this.value)"
                                       class="w-16 text-center bg-slate-900 border border-slate-700 rounded text-white focus:outline-none focus:border-primary-blue">
                                <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                        class="w-8 h-8 bg-slate-700 text-white rounded hover:bg-slate-600 transition-colors">
                                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Item Total -->
                            <div class="text-right">
                                <p class="text-white font-bold text-lg">
                                    $<span class="item-total">{{ number_format($item->product->selling_price * $item->quantity, 2) }}</span>
                                </p>
                            </div>

                            <!-- Remove Button -->
                            <button onclick="removeFromCart({{ $item->id }})"
                                    class="p-2 text-red-400 hover:text-red-300 hover:bg-red-500/20 rounded transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <!-- Clear Cart -->
                    <div class="mt-6 pt-6 border-t border-slate-700">
                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your cart?')">
                            @csrf
                            <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                Clear Shopping Cart
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Continue Shopping -->
                <a href="{{ route('shop') }}" class="inline-flex items-center text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                    Continue Shopping
                </a>
            </div>

            <!-- Order Summary -->
            <div class="lg:w-[350px] mt-8 lg:mt-0">
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-white mb-6">Order Summary</h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-400">
                            <span>Subtotal</span>
                            <span id="subtotal">${{ number_format($subtotal ?? 0, 2) }}</span>
                        </div>

                        @if(isset($vatAmount) && $vatAmount > 0)
                        <div class="flex justify-between text-gray-400">
                            <span>VAT/Tax</span>
                            <span id="vat">${{ number_format($vatAmount, 2) }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between text-gray-400">
                            <span>Shipping</span>
                            <span class="text-green-400">Free</span>
                        </div>

                        <div class="pt-3 border-t border-slate-700">
                            <div class="flex justify-between">
                                <span class="text-white font-bold text-lg">Total</span>
                                <span class="text-primary-blue font-bold text-xl" id="total">
                                    ${{ number_format($total ?? $subtotal ?? 0, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Promo Code -->
                    <div class="mb-6">
                        <label class="block text-gray-400 text-sm mb-2">Promo Code</label>
                        <div class="flex gap-2">
                            <input type="text" placeholder="Enter code"
                                   class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-primary-blue">
                            <button class="px-4 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition-colors">
                                Apply
                            </button>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <a href="{{ route('checkout.index') }}"
                       class="block w-full py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold text-center rounded-lg hover:from-cyan-600 hover:to-blue-600 transition-all">
                        Proceed to Checkout
                    </a>

                    <!-- Security Badge -->
                    <div class="mt-4 flex items-center justify-center gap-2 text-gray-400 text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        Secure Checkout
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="w-24 h-24 mx-auto mb-6 bg-slate-800 border border-slate-700 rounded-full flex items-center justify-center">
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

    fetch(`/cart/${itemId}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ quantity: parseInt(newQuantity) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the UI
            location.reload(); // Simple reload for now
            // In production, update the DOM elements directly
            updateCartCount();
        } else {
            showNotification(data.message || 'Failed to update quantity', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function removeFromCart(itemId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }

    fetch(`/cart/${itemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove item from DOM
            const itemElement = document.querySelector(`[data-item-id="${itemId}"]`);
            if (itemElement) {
                itemElement.remove();
            }

            // Update totals
            if (data.subtotal !== undefined) {
                document.getElementById('subtotal').textContent = '$' + data.subtotal.toFixed(2);
                document.getElementById('total').textContent = '$' + data.subtotal.toFixed(2);
            }

            // Check if cart is empty
            if (data.cartCount === 0) {
                location.reload();
            }

            updateCartCount();
            showNotification('Item removed from cart', 'success');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}
</script>
@endpush

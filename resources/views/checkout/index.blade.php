@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900/20 to-slate-900">
    <div class="container mx-auto px-4 pt-24 pb-16">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-black text-white mb-2">Checkout</h1>
            <p class="text-slate-400">Review and complete your order</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Review -->
            <div class="lg:col-span-2">
                <!-- Shipping Information -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6 mb-6">
                    <h2 class="text-xl font-bold text-white mb-4">Shipping Information</h2>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-slate-400 mb-1">Name</p>
                            <p class="text-white">{{ auth()->user()->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400 mb-1">Email</p>
                            <p class="text-white">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400 mb-1">Phone</p>
                            <p class="text-white">{{ auth()->user()->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400 mb-1">Address</p>
                            <p class="text-white">{{ auth()->user()->address ?? 'Digital delivery - no address required' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6">
                    <h2 class="text-xl font-bold text-white mb-4">Order Items</h2>

                    <div class="space-y-4">
                        @foreach($cartItems as $item)
                        <div class="flex items-center gap-4 pb-4 border-b border-slate-700 last:border-0">
                            <!-- Product Image -->
                            <div class="w-16 h-16 bg-slate-900 rounded-lg overflow-hidden flex-shrink-0">
                                @if($item->product->image)
                                    <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600">
                                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1">
                                <h3 class="font-bold text-white">{{ $item->product->name }}</h3>
                                <p class="text-sm text-slate-400">Qty: {{ $item->quantity }}</p>
                                @if($item->optional_fields_data)
                                    <p class="text-xs text-cyan-400 mt-1">Custom details provided</p>
                                @endif
                            </div>

                            <!-- Price -->
                            <div class="text-right">
                                <p class="text-lg font-bold text-white">
                                    ${{ number_format($item->product->selling_price * $item->quantity, 2) }}
                                </p>
                                @if($item->quantity > 1)
                                <p class="text-xs text-slate-400">
                                    ${{ number_format($item->product->selling_price, 2) }} each
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-white mb-4">Order Summary</h2>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if($vatAmount > 0)
                        <div class="flex justify-between text-slate-400">
                            <span>VAT</span>
                            <span>${{ number_format($vatAmount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-slate-400">
                            <span>Shipping</span>
                            <span class="text-green-400">Free</span>
                        </div>
                    </div>

                    <div class="border-t border-slate-700 pt-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-bold text-white">Total</span>
                            <span class="text-2xl font-black text-cyan-400">
                                ${{ number_format($total, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <form action="{{ route('checkout.process') }}" method="POST" id="payment-form">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-400 mb-3">
                                Payment Method
                            </label>

                            <div class="space-y-2">
                                <!-- Wallet Payment Option -->
                                <label class="flex items-center justify-between p-4 bg-slate-900 border-2 border-transparent rounded-lg cursor-pointer hover:border-primary-blue transition-all payment-method-option">
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" value="wallet"
                                               class="text-primary-blue focus:ring-primary-blue">
                                        <div class="ml-3">
                                            <span class="text-white font-bold block">Wallet Balance</span>
                                            <span class="text-sm text-slate-400">
                                                Available: ${{ number_format(auth()->user()->wallet_balance, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <svg class="w-6 h-6 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </label>

                                @if(auth()->user()->wallet_balance < $total)
                                <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-3">
                                    <p class="text-yellow-400 text-sm flex items-start gap-2">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <span>
                                            Insufficient balance. 
                                            <a href="{{ route('wallet.deposit') }}" class="underline font-bold">Add funds</a> to your wallet.
                                        </span>
                                    </p>
                                </div>
                                @endif

                                <!-- Stripe Payment Option -->
                                <label class="flex items-center justify-between p-4 bg-slate-900 border-2 border-transparent rounded-lg cursor-pointer hover:border-primary-blue transition-all payment-method-option">
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" value="stripe" checked
                                               class="text-primary-blue focus:ring-primary-blue">
                                        <span class="ml-3 text-white font-bold">Credit/Debit Card</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa" class="h-5 opacity-70">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-5 opacity-70">
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Card Details (Stripe Elements will go here) -->
                        <div class="mb-6 hidden" id="card-element-container">
                            <label class="block text-sm font-medium text-slate-400 mb-3">
                                Card Details
                            </label>
                            <div id="card-element" class="p-3 bg-slate-900 rounded-lg">
                                <!-- Stripe Elements will be inserted here -->
                            </div>
                            <div id="card-errors" class="mt-2 text-red-400 text-sm"></div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-6">
                            <label class="flex items-start">
                                <input type="checkbox" required
                                       class="mt-1 text-cyan-500 focus:ring-cyan-500 rounded">
                                <span class="ml-3 text-sm text-slate-400">
                                    I agree to the
                                    <a href="#" class="text-cyan-400 hover:text-cyan-300">Terms and Conditions</a>
                                </span>
                            </label>
                        </div>

                        <!-- Place Order Button -->
                        <button type="submit"
                                class="w-full px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-xl hover:from-cyan-600 hover:to-blue-600 transition-all transform hover:scale-105">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Place Order
                        </button>

                        <!-- Security Badge -->
                        <div class="mt-4 text-center">
                            <div class="inline-flex items-center text-xs text-slate-400">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Secure payment powered by Stripe
                            </div>
                        </div>

                        <!-- Hidden input for Stripe token -->
                        <input type="hidden" name="stripeToken" id="stripeToken">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('payment-form');
        const paymentMethodOptions = document.querySelectorAll('.payment-method-option');
        const walletBalance = {{ auth()->user()->wallet_balance }};
        const orderTotal = {{ $total }};
        
        // Handle payment method selection visual feedback
        paymentMethodOptions.forEach(option => {
            option.addEventListener('click', function() {
                paymentMethodOptions.forEach(opt => {
                    opt.classList.remove('border-primary-blue', 'bg-primary-blue/5');
                });
                this.classList.add('border-primary-blue', 'bg-primary-blue/5');
            });
        });
        
        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            // Check wallet balance if wallet payment is selected
            if (selectedPaymentMethod === 'wallet') {
                if (walletBalance < orderTotal) {
                    alert('Insufficient wallet balance. Please add funds or select another payment method.');
                    return false;
                }
                
                // Confirm wallet payment
                if (!confirm('Pay $' + orderTotal.toFixed(2) + ' from your wallet balance?')) {
                    return false;
                }
                
                // No token needed for wallet payment
                document.getElementById('stripeToken').value = '';
                this.submit();
            } else {
                // Stripe payment - Mock token generation
                document.getElementById('stripeToken').value = 'tok_visa_' + Math.random().toString(36).substr(2, 9);
                this.submit();
            }
        });
    });
</script>
@endpush

@endsection

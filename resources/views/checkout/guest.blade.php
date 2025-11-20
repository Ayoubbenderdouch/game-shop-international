@extends('layouts.app')

@section('title', __('Guest Checkout'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">{{ __('Checkout as Guest') }}</h2>
                
                <form action="{{ route('guest.checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf
                    
                    <!-- Customer Information -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ __('Your Information') }}
                        </h3>
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Full Name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-blue focus:border-transparent"
                                    placeholder="{{ __('Enter your name') }}" value="{{ old('name') }}">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Email Address') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-blue focus:border-transparent"
                                    placeholder="{{ __('your@email.com') }}" value="{{ old('email') }}">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Phone Number') }} <span class="text-gray-400">({{ __('Optional') }})</span>
                                </label>
                                <input type="tel" name="phone" id="phone"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-blue focus:border-transparent"
                                    placeholder="+1234567890" value="{{ old('phone') }}">
                            </div>
                            
                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Country') }}
                                </label>
                                <select name="country" id="country"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-blue focus:border-transparent">
                                    <option value="">{{ __('Select Country') }}</option>
                                    @php
                                        $countries = \App\Models\Country::where('is_active', true)->get();
                                    @endphp
                                    @foreach($countries as $countryModel)
                                        <option value="{{ $countryModel->code }}" {{ old('country') == $countryModel->code ? 'selected' : '' }}>
                                            {{ $countryModel->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            {{ __('Payment Method') }}
                        </h3>
                        
                        <div class="space-y-4">
                            <!-- Stripe -->
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary-blue transition-colors">
                                <input type="radio" name="payment_method" value="stripe" checked class="w-5 h-5 text-primary-blue">
                                <span class="ml-3 flex items-center gap-3 flex-1">
                                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.5 15C10.5 16.125 9.75 17.25 8.25 17.25C6.75 17.25 6 16.125 6 15C6 13.875 6.75 12.75 8.25 12.75C9.75 12.75 10.5 13.875 10.5 15Z" fill="#6772E5"/>
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2 6C2 4.89543 2.89543 4 4 4H20C21.1046 4 22 4.89543 22 6V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V6ZM4 6H20V8H4V6ZM4 10H20V18H4V10Z" fill="#6772E5"/>
                                    </svg>
                                    <span class="font-semibold">{{ __('Credit / Debit Card') }}</span>
                                    <span class="text-sm text-gray-500 ml-auto">{{ __('Visa, Mastercard, Amex') }}</span>
                                </span>
                            </label>
                            
                            <!-- PayPal -->
                            <label class="flex items-center p-4 border-2 border-gray-300 rounded-lg cursor-pointer hover:border-primary-blue transition-colors">
                                <input type="radio" name="payment_method" value="paypal" class="w-5 h-5 text-primary-blue">
                                <span class="ml-3 flex items-center gap-3 flex-1">
                                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="#003087" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18.75 6.94C18.68 6.38 18.35 5.86 17.87 5.48C17.22 4.97 16.38 4.75 15.44 4.75H9.31C9.12 4.75 8.94 4.85 8.85 5.02L6.51 11.39C6.46 11.5 6.51 11.63 6.61 11.7C6.71 11.77 6.85 11.77 6.95 11.7L10.41 9.24C10.54 9.15 10.69 9.11 10.85 9.13L13.98 9.43C14.72 9.5 15.44 9.23 15.94 8.69C16.44 8.15 16.67 7.41 16.56 6.68L16.48 6.23C16.45 6.07 16.53 5.91 16.67 5.83C16.81 5.75 16.98 5.77 17.1 5.87C17.47 6.16 17.7 6.61 17.74 7.09L17.88 8.67C18.04 10.25 17.35 11.78 16.08 12.69C14.81 13.6 13.12 13.77 11.71 13.13L7.99 11.5L6.16 16.25C6.08 16.45 6.16 16.68 6.34 16.79C6.52 16.9 6.76 16.86 6.89 16.7L9.46 13.5C9.57 13.36 9.74 13.28 9.92 13.29L13.69 13.43C15.14 13.49 16.55 12.88 17.49 11.75C18.43 10.62 18.78 9.1 18.45 7.66L18.75 6.94Z"/>
                                    </svg>
                                    <span class="font-semibold">PayPal</span>
                                    <span class="text-sm text-gray-500 ml-auto">{{ __('Fast & Secure') }}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Stripe Card Input (shown when Stripe selected) -->
                    <div id="stripeCardElement" class="mb-8">
                        <label for="card-element" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Card Details') }}
                        </label>
                        <div id="card-element" class="w-full px-4 py-3 rounded-lg border border-gray-300"></div>
                        <div id="card-errors" class="text-red-500 text-sm mt-2"></div>
                    </div>
                    
                    <!-- Terms -->
                    <div class="mb-6">
                        <label class="flex items-start gap-3">
                            <input type="checkbox" required class="w-5 h-5 mt-0.5 text-primary-blue rounded">
                            <span class="text-sm text-gray-600">
                                {{ __('I agree to the') }} 
                                <a href="{{ route('legal.terms') }}" target="_blank" class="text-primary-blue hover:underline">{{ __('Terms & Conditions') }}</a>
                                {{ __('and') }}
                                <a href="{{ route('legal.privacy') }}" target="_blank" class="text-primary-blue hover:underline">{{ __('Privacy Policy') }}</a>
                            </span>
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-primary-blue to-blue-600 text-white py-4 rounded-lg font-bold text-lg hover:shadow-lg hover:shadow-primary-blue/50 transition-all duration-300">
                        {{ __('Place Order') }} - {{ app(\App\Services\CurrencyService::class)->formatPrice($total) }}
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-200 sticky top-24">
                <h3 class="text-xl font-bold text-gray-900 mb-6">{{ __('Order Summary') }}</h3>
                
                <!-- Products List -->
                <div class="space-y-4 mb-6">
                    @foreach($products as $item)
                        <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                            @if($item['product']->image)
                                <img src="{{ asset('storage/' . $item['product']->image) }}" alt="{{ $item['product']->name }}" class="w-16 h-16 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 text-sm">{{ $item['product']->name }}</h4>
                                <p class="text-gray-500 text-xs">{{ __('Qty') }}: {{ $item['quantity'] }}</p>
                            </div>
                            
                            <div class="text-right">
                                <p class="font-bold text-gray-900">{{ app(\App\Services\CurrencyService::class)->formatPrice($item['total']) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pricing Details -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-gray-600">
                        <span>{{ __('Subtotal') }}</span>
                        <span class="font-semibold">{{ app(\App\Services\CurrencyService::class)->formatPrice($subtotal) }}</span>
                    </div>
                    
                    @if($vatAmount > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>{{ __('VAT') }} ({{ number_format($vatRate, 0) }}%)</span>
                            <span class="font-semibold">{{ app(\App\Services\CurrencyService::class)->formatPrice($vatAmount) }}</span>
                        </div>
                    @endif
                    
                    <div class="pt-3 border-t border-gray-200">
                        <div class="flex justify-between text-lg font-bold text-gray-900">
                            <span>{{ __('Total') }}</span>
                            <span class="text-primary-blue">{{ app(\App\Services\CurrencyService::class)->formatPrice($total) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 text-right">{{ __('in') }} {{ $currency }}</p>
                    </div>
                </div>
                
                <!-- Security Badge -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('Secure SSL Encrypted Payment') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    // Initialize Stripe
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    const elements = stripe.elements();
    
    // Create card element
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        }
    });
    
    cardElement.mount('#card-element');
    
    // Handle card errors
    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
    
    // Show/hide card element based on payment method
    document.querySelectorAll('input[name="payment_method"]').forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('stripeCardElement').style.display = 
                this.value === 'stripe' ? 'block' : 'none';
        });
    });
    
    // Handle form submission
    const form = document.getElementById('checkoutForm');
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (paymentMethod === 'stripe') {
            // Create token with Stripe
            const {token, error} = await stripe.createToken(cardElement);
            
            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                return;
            }
            
            // Add token to form
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = 'stripeToken';
            tokenInput.value = token.id;
            form.appendChild(tokenInput);
        }
        
        // Submit form
        form.submit();
    });
</script>
@endsection

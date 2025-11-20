@extends('layouts.app')

@section('title', __('Add Funds') . ' - GameShop')

@section('header')
    <h1 class="text-white text-[42px] font-bold mb-4">{{ __('Add Funds') }}</h1>
    <nav class="flex items-center space-x-2 text-sm">
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-all">{{ __('Home') }}</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('wallet.index') }}" class="text-gray-400 hover:text-white transition-all">{{ __('Wallet') }}</a>
        <span class="text-gray-400">/</span>
        <span class="text-white">{{ __('Add Funds') }}</span>
    </nav>
@endsection

@section('content')
<div class="w-full py-[60px]">
    <div class="max-w-[800px] mx-auto px-5 lg:px-0">
        
        <!-- Current Balance -->
        <div class="bg-black border border-[#23262B] rounded-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">{{ __('Current Balance') }}</p>
                    <p class="text-3xl font-bold text-white">
                        {{ number_format($user->wallet_balance, 2) }} {{ config('app.currency', 'EUR') }}
                    </p>
                </div>
                <div class="w-16 h-16 bg-primary-blue/20 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Deposit Form -->
        <div class="bg-black border border-[#23262B] rounded-lg p-8">
            <h2 class="text-2xl font-bold text-white mb-6">{{ __('Select Amount') }}</h2>
            
            <form action="{{ route('wallet.deposit.process') }}" method="POST" id="depositForm">
                @csrf
                
                <!-- Predefined Amounts -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                    @foreach($amounts as $amount)
                    <button type="button" 
                            class="amount-btn p-6 bg-[#23262B] border-2 border-transparent rounded-lg hover:border-primary-blue transition-all text-center group"
                            data-amount="{{ $amount }}">
                        <p class="text-3xl font-bold text-white mb-1">{{ $amount }}</p>
                        <p class="text-gray-400 text-sm">{{ config('app.currency', 'EUR') }}</p>
                    </button>
                    @endforeach
                </div>

                <!-- Custom Amount -->
                <div class="mb-6">
                    <label class="block text-white font-bold mb-2">{{ __('Or Enter Custom Amount') }}</label>
                    <div class="relative">
                        <input type="number" 
                               name="amount" 
                               id="customAmount"
                               min="5" 
                               max="10000" 
                               step="0.01"
                               class="w-full bg-[#23262B] border border-gray-700 rounded-lg px-4 py-4 text-white text-2xl font-bold focus:border-primary-blue focus:outline-none"
                               placeholder="0.00"
                               required>
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xl font-bold">
                            {{ config('app.currency', 'EUR') }}
                        </span>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">
                        {{ __('Minimum: 5') }} {{ config('app.currency', 'EUR') }} | 
                        {{ __('Maximum: 10,000') }} {{ config('app.currency', 'EUR') }}
                    </p>
                    @error('amount')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Info -->
                <div class="bg-[#23262B] rounded-lg p-6 mb-6">
                    <div class="flex items-start gap-3 mb-4">
                        <svg class="w-6 h-6 text-primary-blue flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-white font-bold mb-2">{{ __('Payment Information') }}</p>
                            <ul class="text-gray-400 text-sm space-y-1">
                                <li>✓ {{ __('Secure payment via Stripe') }}</li>
                                <li>✓ {{ __('Instant credit to your wallet') }}</li>
                                <li>✓ {{ __('All major credit cards accepted') }}</li>
                                <li>✓ {{ __('SSL encrypted transaction') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4">
                    <a href="{{ route('wallet.index') }}" 
                       class="flex-1 bg-[#23262B] text-white px-6 py-4 rounded-lg font-bold hover:bg-[#2a2d33] transition-all text-center">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-primary-blue to-[#3fda74] text-black px-6 py-4 rounded-lg font-bold hover:opacity-90 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        {{ __('Proceed to Payment') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Payment Methods -->
        <div class="mt-8 text-center">
            <p class="text-gray-400 text-sm mb-4">{{ __('We accept') }}</p>
            <div class="flex items-center justify-center gap-6">
                <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa" class="h-8 opacity-70">
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-8 opacity-70">
                <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/American_Express_logo_%282018%29.svg" alt="Amex" class="h-8 opacity-70">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountButtons = document.querySelectorAll('.amount-btn');
    const customAmountInput = document.getElementById('customAmount');
    
    // Handle predefined amount buttons
    amountButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const amount = this.dataset.amount;
            customAmountInput.value = amount;
            
            // Remove active class from all buttons
            amountButtons.forEach(b => {
                b.classList.remove('border-primary-blue', 'bg-primary-blue/10');
            });
            
            // Add active class to clicked button
            this.classList.add('border-primary-blue', 'bg-primary-blue/10');
        });
    });
    
    // Clear button selection when typing custom amount
    customAmountInput.addEventListener('input', function() {
        amountButtons.forEach(btn => {
            btn.classList.remove('border-primary-blue', 'bg-primary-blue/10');
        });
    });
});
</script>
@endsection

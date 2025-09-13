@extends('layouts.app')

@section('title', 'Order Successful')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900/20 to-slate-900">
    <div class="container mx-auto px-4 pt-24 pb-16">
        <!-- Success Animation Container -->
        <div class="max-w-2xl mx-auto">
            <!-- Success Icon Animation -->
            <div class="text-center mb-8">
                <div class="relative inline-block">
                    <div class="absolute inset-0 bg-green-500 rounded-full blur-xl opacity-50 animate-pulse"></div>
                    <div class="relative w-24 h-24 mx-auto bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-2xl p-8 text-center mb-8">
                <h1 class="text-4xl font-black text-white mb-4">Order Confirmed!</h1>
                <p class="text-xl text-slate-300 mb-6">Thank you for your purchase!</p>

                <!-- Order Number -->
                <div class="bg-slate-900/50 rounded-xl p-4 mb-6">
                    <p class="text-sm text-slate-400 mb-2">Order Number</p>
                    <p class="text-2xl font-bold text-cyan-400">#{{ $order->order_number ?? str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</p>
                </div>

                <p class="text-slate-400 mb-6">
                    We've sent a confirmation email to <span class="text-white font-medium">{{ auth()->user()->email }}</span>
                </p>

                <!-- Order Summary -->
                <div class="border-t border-slate-700 pt-6 mb-6">
                    <h2 class="text-lg font-bold text-white mb-4">Order Summary</h2>

                    <div class="space-y-3 text-left">
                        @foreach($order->orderItems as $item)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-slate-900 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ $item->product->image }}" alt="{{ $item->product_name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-600">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $item->product_name }}</p>
                                    <p class="text-xs text-slate-400">Qty: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <p class="text-white font-medium">${{ number_format($item->total_price, 2) }}</p>
                        </div>
                        @endforeach
                    </div>

                    <!-- Total -->
                    <div class="border-t border-slate-700 mt-4 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-white">Total Paid</span>
                            <span class="text-2xl font-black text-green-400">
                                ${{ number_format($order->total_amount ?? $order->total ?? 0, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-blue-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-left">
                            <p class="text-blue-400 font-medium mb-1">Digital Delivery</p>
                            <p class="text-sm text-slate-400">
                                Your digital items will be delivered to your account within 24 hours.
                                You'll receive an email notification once they're ready.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('orders.show', $order) }}"
                       class="flex-1 px-6 py-3 bg-cyan-500 text-white font-bold rounded-xl hover:bg-cyan-600 transition-all text-center">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Order Details
                    </a>
                    <a href="{{ route('shop') }}"
                       class="flex-1 px-6 py-3 bg-slate-700 text-white font-bold rounded-xl hover:bg-slate-600 transition-all text-center">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Customer Support -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl p-4 text-center">
                    <svg class="w-8 h-8 text-cyan-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <h3 class="font-bold text-white mb-1">24/7 Support</h3>
                    <p class="text-xs text-slate-400">Need help? Contact our support team anytime</p>
                </div>

                <!-- Secure Payment -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl p-4 text-center">
                    <svg class="w-8 h-8 text-green-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    <h3 class="font-bold text-white mb-1">Secure Payment</h3>
                    <p class="text-xs text-slate-400">Your payment information is always protected</p>
                </div>

                <!-- Fast Delivery -->
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl p-4 text-center">
                    <svg class="w-8 h-8 text-purple-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <h3 class="font-bold text-white mb-1">Instant Delivery</h3>
                    <p class="text-xs text-slate-400">Digital items delivered within 24 hours</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes checkmark {
        0% {
            stroke-dashoffset: 100;
        }
        100% {
            stroke-dashoffset: 0;
        }
    }

    .animate-checkmark {
        stroke-dasharray: 100;
        animation: checkmark 0.5s ease-in-out;
    }
</style>
@endpush

@push('scripts')
<script>
    // Confetti animation on load
    document.addEventListener('DOMContentLoaded', function() {
        // Simple confetti effect
        const colors = ['#06b6d4', '#8b5cf6', '#10b981', '#f59e0b'];
        const confettiCount = 50;

        for (let i = 0; i < confettiCount; i++) {
            setTimeout(() => {
                const confetti = document.createElement('div');
                confetti.style.position = 'fixed';
                confetti.style.width = '10px';
                confetti.style.height = '10px';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.top = '-10px';
                confetti.style.opacity = Math.random() + 0.5;
                confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
                confetti.style.transition = 'all 3s ease-out';
                confetti.style.zIndex = '9999';
                document.body.appendChild(confetti);

                setTimeout(() => {
                    confetti.style.top = '100%';
                    confetti.style.transform = `rotate(${Math.random() * 720}deg)`;
                    confetti.style.opacity = '0';
                }, 100);

                setTimeout(() => {
                    confetti.remove();
                }, 3100);
            }, i * 30);
        }
    });
</script>
@endpush

@endsection

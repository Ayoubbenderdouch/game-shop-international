@extends('layouts.app')

@section('title', __('app.freefire.how_to_redeem'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-4 text-orange-400">{{ __('app.freefire.how_to_redeem') }}</h1>
        <p class="text-xl text-gray-300">Step-by-step guide to redeem your Free Fire diamonds</p>
    </div>

    <!-- Main Instructions -->
    <div class="bg-gray-800 rounded-lg p-8 mb-8 border-2 border-orange-500">
        <h2 class="text-2xl font-bold mb-6 text-orange-400">{{ __('app.freefire.how_to_redeem') }}</h2>

        <div class="space-y-6">
            <!-- Step 1 -->
            <div class="flex {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }} gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold">1</span>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-lg mb-2">{{ __('app.freefire.redeem_step_1') }}</h3>
                    <p class="text-gray-400">
                        Visit <a href="https://shop.garena.sg/app" target="_blank" class="text-orange-400 hover:underline">{{ __('app.freefire.redeem_link') }}</a>
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }} gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold">2</span>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-lg mb-2">{{ __('app.freefire.redeem_step_2') }}</h3>
                    <p class="text-gray-400">Select the Player ID method for delivery</p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }} gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold">3</span>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-lg mb-2">{{ __('app.freefire.redeem_step_3') }}</h3>
                    <p class="text-gray-400">Log in with your Player ID</p>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="flex {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }} gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                    <span class="text-white font-bold">4</span>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-lg mb-2">{{ __('app.freefire.redeem_step_4') }}</h3>
                    <p class="text-gray-400">Choose Garena PPC option and enter your redemption code</p>
                </div>
            </div>
        </div>
    </div>

    <!-- How to Find Player ID -->
    <div class="bg-gray-800 rounded-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-6 text-orange-400">{{ __('app.freefire.how_to_find_id') }}</h2>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div class="flex {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }} gap-3">
                    <span class="text-orange-400 font-bold">1.</span>
                    <p>{{ __('app.freefire.find_id_step_1') }}</p>
                </div>
                <div class="flex {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }} gap-3">
                    <span class="text-orange-400 font-bold">2.</span>
                    <p>{{ __('app.freefire.find_id_step_2') }}</p>
                </div>
                <div class="flex {{ app()->getLocale() == 'ar' ? 'flex-row-reverse' : '' }} gap-3">
                    <span class="text-orange-400 font-bold">3.</span>
                    <p>{{ __('app.freefire.find_id_step_3') }}</p>
                </div>
            </div>

            <div class="bg-gray-900 rounded-lg p-6">
                <img src="https://via.placeholder.com/400x300/1a1a1a/ff6b35?text=Player+ID+Location"
                     alt="Where to find Player ID"
                     class="w-full rounded">
                <p class="text-xs text-gray-500 mt-2 text-center">Sample screenshot showing Player ID location</p>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-gray-800 rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-orange-400">Frequently Asked Questions</h2>

        <div class="space-y-4">
            <div>
                <h3 class="font-semibold mb-2">How long does it take to receive my diamonds?</h3>
                <p class="text-gray-400">Diamonds are usually delivered instantly after successful redemption, but may take up to 5 minutes during peak times.</p>
            </div>

            <div>
                <h3 class="font-semibold mb-2">What if my redemption code doesn't work?</h3>
                <p class="text-gray-400">Please double-check that you've entered the code correctly. If issues persist, contact our support team with your order details.</p>
            </div>

            <div>
                <h3 class="font-semibold mb-2">Can I use the code on any server?</h3>
                <p class="text-gray-400">Yes, Free Fire redemption codes work across all servers. Just make sure to use the correct Player ID.</p>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center mt-8">
        <a href="/freefire" class="neon-button-orange px-8 py-3 rounded-lg inline-block font-semibold text-lg">
            {{ __('app.freefire.buy_diamonds_now') }}
        </a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', __('app.freefire.success_title'))

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <div class="bg-gray-800 rounded-lg p-8 border-2 border-green-500">
        <div class="text-6xl mb-4">✅</div>

        <h1 class="text-3xl font-bold mb-4 text-green-500">{{ __('app.freefire.success_title') }}</h1>

        <p class="text-xl mb-6">
            {{ __('app.freefire.success_message', ['amount' => $order->diamond_amount]) }}
        </p>

        @if($order->redemption_code)
        <div class="bg-orange-900/20 border-2 border-orange-500 rounded-lg p-6 mb-6">
            <p class="text-sm text-orange-400 mb-3">{{ __('app.freefire.redemption_code') }}:</p>
            <div class="bg-gray-900 rounded p-4 mb-3">
                <code class="text-2xl font-mono font-bold text-orange-400">{{ $order->redemption_code }}</code>
            </div>
            <p class="text-xs text-gray-400">{{ __('app.freefire.redemption_instructions') }}</p>
        </div>
        @endif

        <div class="bg-gray-900 rounded p-4 mb-6">
            <p class="text-sm text-gray-400 mb-2">{{ __('app.freefire.transaction_details') }}</p>
            <div class="space-y-2 text-sm {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                <p><strong>{{ __('app.freefire.player_id') }}:</strong> {{ $order->player_id }}</p>
                <p><strong>{{ __('app.freefire.diamond_amount') }}:</strong> {{ $order->diamond_amount }} 💎</p>
                <p><strong>{{ __('app.freefire.transaction_id') }}:</strong> {{ $order->transaction_id }}</p>
                <p><strong>{{ __('app.freefire.date') }}:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        <div class="flex justify-center space-x-4 {{ app()->getLocale() == 'ar' ? 'space-x-reverse' : '' }}">
            <a href="/freefire" class="neon-button-orange px-6 py-2 rounded">
                {{ __('app.freefire.buy_more_diamonds') }}
            </a>
            <a href="/freefire/orders" class="bg-gray-700 px-6 py-2 rounded hover:bg-gray-600 transition">
                {{ __('app.freefire.view_orders') }}
            </a>
        </div>
    </div>
</div>
@endsection

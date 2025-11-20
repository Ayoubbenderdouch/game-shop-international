@extends('layouts.app')

@section('title', __('Wallet') . ' - GameShop')

@section('header')
    <h1 class="text-white text-[42px] font-bold mb-4">{{ __('My Wallet') }}</h1>
    <nav class="flex items-center space-x-2 text-sm">
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-all">{{ __('Home') }}</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white transition-all">{{ __('Dashboard') }}</a>
        <span class="text-gray-400">/</span>
        <span class="text-white">{{ __('Wallet') }}</span>
    </nav>
@endsection

@section('content')
<div class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        
        <!-- Wallet Balance Card -->
        <div class="bg-gradient-to-r from-primary-blue to-[#3fda74] rounded-2xl p-8 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-black/80 text-sm mb-2">{{ __('Available Balance') }}</p>
                    <h2 class="text-5xl font-black text-black mb-4">
                        {{ number_format($user->wallet_balance, 2) }} {{ config('app.currency', 'EUR') }}
                    </h2>
                    <p class="text-black/70 text-sm">{{ __('Use your wallet to make instant purchases') }}</p>
                </div>
                <a href="{{ route('wallet.deposit') }}" class="bg-black text-white px-8 py-4 rounded-lg font-bold hover:bg-gray-900 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Add Funds') }}
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <!-- Total Deposits -->
            <div class="bg-black border border-[#23262B] rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                        </svg>
                    </div>
                    <span class="text-gray-400 text-sm">{{ __('Deposits') }}</span>
                </div>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['total_deposits'], 2) }}</p>
                <p class="text-gray-400 text-sm mt-2">{{ config('app.currency', 'EUR') }}</p>
            </div>

            <!-- Total Spent -->
            <div class="bg-black border border-[#23262B] rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                        </svg>
                    </div>
                    <span class="text-gray-400 text-sm">{{ __('Spent') }}</span>
                </div>
                <p class="text-3xl font-bold text-white">{{ number_format($stats['total_spent'], 2) }}</p>
                <p class="text-gray-400 text-sm mt-2">{{ config('app.currency', 'EUR') }}</p>
            </div>

            <!-- Total Transactions -->
            <div class="bg-black border border-[#23262B] rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-primary-blue/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <span class="text-gray-400 text-sm">{{ __('Total') }}</span>
                </div>
                <p class="text-3xl font-bold text-white">{{ $stats['total_transactions'] }}</p>
                <p class="text-gray-400 text-sm mt-2">{{ __('Transactions') }}</p>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-black border border-[#23262B] rounded-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">{{ __('Recent Transactions') }}</h3>
                <a href="{{ route('wallet.history') }}" class="text-primary-blue hover:text-[#3fda74] transition-all font-bold">
                    {{ __('View All') }} →
                </a>
            </div>

            @if($transactions->count() > 0)
            <div class="space-y-4">
                @foreach($transactions as $transaction)
                <div class="flex items-center justify-between p-4 bg-[#23262B] rounded-lg hover:bg-[#2a2d33] transition-all">
                    <div class="flex items-center gap-4">
                        <!-- Transaction Icon -->
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center
                            @if($transaction->type == 'deposit' || $transaction->type == 'refund')
                                bg-green-500/20
                            @elseif($transaction->type == 'purchase' || $transaction->type == 'withdraw')
                                bg-red-500/20
                            @else
                                bg-blue-500/20
                            @endif">
                            @if($transaction->type == 'deposit' || $transaction->type == 'refund')
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                </svg>
                            @elseif($transaction->type == 'purchase' || $transaction->type == 'withdraw')
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @endif
                        </div>
                        
                        <!-- Transaction Details -->
                        <div>
                            <p class="text-white font-bold">
                                {{ ucfirst($transaction->type) }}
                            </p>
                            <p class="text-gray-400 text-sm">
                                {{ $transaction->description ?? '-' }}
                            </p>
                            <p class="text-gray-500 text-xs mt-1">
                                {{ $transaction->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-lg font-bold
                            @if($transaction->type == 'deposit' || $transaction->type == 'refund')
                                text-green-500
                            @elseif($transaction->type == 'purchase' || $transaction->type == 'withdraw')
                                text-red-500
                            @else
                                text-white
                            @endif">
                            @if($transaction->type == 'deposit' || $transaction->type == 'refund')
                                +
                            @else
                                -
                            @endif
                            {{ number_format($transaction->amount, 2) }} {{ config('app.currency', 'EUR') }}
                        </p>
                        <p class="text-gray-400 text-sm">
                            {{ __('Balance') }}: {{ number_format($transaction->balance_after, 2) }}
                        </p>
                        <span class="inline-block px-2 py-1 text-xs rounded-full mt-1
                            @if($transaction->status == 'completed')
                                bg-green-500/20 text-green-500
                            @elseif($transaction->status == 'pending')
                                bg-yellow-500/20 text-yellow-500
                            @elseif($transaction->status == 'failed')
                                bg-red-500/20 text-red-500
                            @else
                                bg-gray-500/20 text-gray-500
                            @endif">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-xl font-bold text-gray-400 mb-2">{{ __('No Transactions Yet') }}</h3>
                <p class="text-gray-500 mb-6">{{ __('Start by adding funds to your wallet') }}</p>
                <a href="{{ route('wallet.deposit') }}" class="inline-block bg-primary-blue text-black px-8 py-3 rounded-lg font-bold hover:bg-[#3fda74] transition-all">
                    {{ __('Add Funds Now') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', __('Transaction History') . ' - GameShop')

@section('header')
    <h1 class="text-white text-[42px] font-bold mb-4">{{ __('Transaction History') }}</h1>
    <nav class="flex items-center space-x-2 text-sm">
        <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-all">{{ __('Home') }}</a>
        <span class="text-gray-400">/</span>
        <a href="{{ route('wallet.index') }}" class="text-gray-400 hover:text-white transition-all">{{ __('Wallet') }}</a>
        <span class="text-gray-400">/</span>
        <span class="text-white">{{ __('History') }}</span>
    </nav>
@endsection

@section('content')
<div class="w-full py-[60px]">
    <div class="max-w-[1170px] mx-auto px-5 lg:px-0">
        
        <!-- Filters -->
        <div class="bg-black border border-[#23262B] rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('wallet.history') }}" class="grid md:grid-cols-4 gap-4">
                <!-- Type Filter -->
                <div>
                    <label class="block text-gray-400 text-sm mb-2">{{ __('Type') }}</label>
                    <select name="type" class="w-full bg-[#23262B] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-primary-blue focus:outline-none">
                        <option value="all">{{ __('All Types') }}</option>
                        <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>{{ __('Deposit') }}</option>
                        <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>{{ __('Purchase') }}</option>
                        <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>{{ __('Refund') }}</option>
                        <option value="withdraw" {{ request('type') == 'withdraw' ? 'selected' : '' }}>{{ __('Withdraw') }}</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-gray-400 text-sm mb-2">{{ __('Status') }}</label>
                    <select name="status" class="w-full bg-[#23262B] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-primary-blue focus:outline-none">
                        <option value="all">{{ __('All Status') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-gray-400 text-sm mb-2">{{ __('From Date') }}</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="w-full bg-[#23262B] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-primary-blue focus:outline-none">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-gray-400 text-sm mb-2">{{ __('To Date') }}</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="w-full bg-[#23262B] border border-gray-700 rounded-lg px-4 py-3 text-white focus:border-primary-blue focus:outline-none">
                </div>

                <!-- Buttons -->
                <div class="md:col-span-4 flex gap-4">
                    <button type="submit" class="bg-primary-blue text-black px-6 py-3 rounded-lg font-bold hover:bg-[#3fda74] transition-all">
                        {{ __('Apply Filters') }}
                    </button>
                    <a href="{{ route('wallet.history') }}" class="bg-[#23262B] text-white px-6 py-3 rounded-lg font-bold hover:bg-[#2a2d33] transition-all">
                        {{ __('Clear Filters') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Transactions List -->
        <div class="bg-black border border-[#23262B] rounded-lg overflow-hidden">
            @if($transactions->count() > 0)
            <!-- Desktop View -->
            <div class="hidden lg:block">
                <table class="w-full">
                    <thead class="bg-[#23262B]">
                        <tr>
                            <th class="text-left text-gray-400 text-sm font-bold p-4">{{ __('Date') }}</th>
                            <th class="text-left text-gray-400 text-sm font-bold p-4">{{ __('Type') }}</th>
                            <th class="text-left text-gray-400 text-sm font-bold p-4">{{ __('Description') }}</th>
                            <th class="text-right text-gray-400 text-sm font-bold p-4">{{ __('Amount') }}</th>
                            <th class="text-right text-gray-400 text-sm font-bold p-4">{{ __('Balance') }}</th>
                            <th class="text-center text-gray-400 text-sm font-bold p-4">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                        <tr class="border-t border-[#23262B] hover:bg-[#23262B]/50 transition-all">
                            <td class="p-4">
                                <p class="text-white text-sm">{{ $transaction->created_at->format('d M Y') }}</p>
                                <p class="text-gray-400 text-xs">{{ $transaction->created_at->format('H:i') }}</p>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    @if($transaction->type == 'deposit' || $transaction->type == 'refund')
                                        <div class="w-8 h-8 bg-green-500/20 rounded flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-red-500/20 rounded flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="text-white font-bold capitalize">{{ $transaction->type }}</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <p class="text-white">{{ $transaction->description ?? '-' }}</p>
                                @if($transaction->reference_id)
                                <p class="text-gray-400 text-xs mt-1">{{ __('Ref') }}: {{ $transaction->reference_id }}</p>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <p class="text-lg font-bold
                                    @if($transaction->type == 'deposit' || $transaction->type == 'refund')
                                        text-green-500
                                    @else
                                        text-red-500
                                    @endif">
                                    @if($transaction->type == 'deposit' || $transaction->type == 'refund')
                                        +
                                    @else
                                        -
                                    @endif
                                    {{ number_format($transaction->amount, 2) }}
                                </p>
                                <p class="text-gray-400 text-xs">{{ config('app.currency', 'EUR') }}</p>
                            </td>
                            <td class="p-4 text-right">
                                <p class="text-white font-bold">{{ number_format($transaction->balance_after, 2) }}</p>
                                <p class="text-gray-400 text-xs">{{ config('app.currency', 'EUR') }}</p>
                            </td>
                            <td class="p-4 text-center">
                                <span class="inline-block px-3 py-1 text-xs rounded-full font-bold
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
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="lg:hidden space-y-4 p-4">
                @foreach($transactions as $transaction)
                <div class="bg-[#23262B] rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-3">
                            @if($transaction->type == 'deposit' || $transaction->type == 'refund')
                                <div class="w-10 h-10 bg-green-500/20 rounded flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-red-500/20 rounded flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-white font-bold capitalize">{{ $transaction->type }}</p>
                                <p class="text-gray-400 text-xs">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <span class="inline-block px-2 py-1 text-xs rounded-full font-bold
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
                    <p class="text-gray-300 text-sm mb-3">{{ $transaction->description ?? '-' }}</p>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-700">
                        <div>
                            <p class="text-gray-400 text-xs mb-1">{{ __('Amount') }}</p>
                            <p class="text-lg font-bold
                                @if($transaction->type == 'deposit' || $transaction->type == 'refund')
                                    text-green-500
                                @else
                                    text-red-500
                                @endif">
                                @if($transaction->type == 'deposit' || $transaction->type == 'refund')
                                    +
                                @else
                                    -
                                @endif
                                {{ number_format($transaction->amount, 2) }} {{ config('app.currency', 'EUR') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-400 text-xs mb-1">{{ __('Balance After') }}</p>
                            <p class="text-white font-bold">{{ number_format($transaction->balance_after, 2) }} {{ config('app.currency', 'EUR') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
            <div class="p-6 border-t border-[#23262B]">
                {{ $transactions->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-16 px-4">
                <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-xl font-bold text-gray-400 mb-2">{{ __('No Transactions Found') }}</h3>
                <p class="text-gray-500 mb-6">{{ __('Try adjusting your filters') }}</p>
                <a href="{{ route('wallet.index') }}" class="inline-block bg-primary-blue text-black px-6 py-3 rounded-lg font-bold hover:bg-[#3fda74] transition-all">
                    {{ __('Back to Wallet') }}
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

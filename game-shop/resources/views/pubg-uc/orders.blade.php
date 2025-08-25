@extends('layouts.app')

@section('title', __('pubg.orders_title'))

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2">{{ __('pubg.my_uc_orders') }}</h1>
        <p class="text-gray-400">{{ __('pubg.orders_subtitle') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-sm">{{ __('pubg.total_orders') }}</span>
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 002 0h6a1 1 0 100 2 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-white">{{ $orders->total() }}</p>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-sm">{{ __('pubg.total_uc_purchased') }}</span>
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-yellow-400">
                {{ number_format($orders->where('status', 'completed')->sum('uc_amount')) }} UC
            </p>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-sm">{{ __('pubg.total_spent') }}</span>
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-green-400">
                ${{ number_format($orders->where('status', 'completed')->sum('price'), 2) }}
            </p>
        </div>

        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-400 text-sm">{{ __('pubg.success_rate') }}</span>
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-2xl font-bold text-green-400">
                @if($orders->count() > 0)
                    {{ round(($orders->where('status', 'completed')->count() / $orders->count()) * 100) }}%
                @else
                    0%
                @endif
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-800 rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('pubg-uc.orders') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('pubg.search_by_player_id') }}"
                       class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500">
            </div>
            <select name="status"
                    class="bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                <option value="">{{ __('pubg.all_status') }}</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('pubg.status_completed') }}</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('pubg.status_pending') }}</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>{{ __('pubg.status_processing') }}</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>{{ __('pubg.status_failed') }}</option>
            </select>
            <select name="sort"
                    class="bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ __('pubg.sort_latest') }}</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('pubg.sort_oldest') }}</option>
                <option value="amount_high" {{ request('sort') == 'amount_high' ? 'selected' : '' }}>{{ __('pubg.sort_amount_high') }}</option>
                <option value="amount_low" {{ request('sort') == 'amount_low' ? 'selected' : '' }}>{{ __('pubg.sort_amount_low') }}</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-yellow-500 text-gray-900 font-bold rounded hover:bg-yellow-400 transition">
                {{ __('pubg.filter') }}
            </button>
            @if(request()->hasAny(['search', 'status', 'sort']))
                <a href="{{ route('pubg-uc.orders') }}" class="px-6 py-2 bg-gray-700 text-white rounded hover:bg-gray-600 transition">
                    {{ __('pubg.clear') }}
                </a>
            @endif
        </form>
    </div>

    <!-- Orders List -->
    @if($orders->isEmpty())
        <div class="text-center py-16 bg-gray-800 rounded-lg">
            <div class="mb-4">
                <svg class="w-24 h-24 mx-auto text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 002 0h6a1 1 0 100 2 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="text-xl text-gray-400 mb-4">{{ __('pubg.no_orders_yet') }}</p>
            <p class="text-gray-500 mb-6">{{ __('pubg.no_orders_description') }}</p>
            <a href="/pubg-uc" class="neon-button px-8 py-3 rounded-lg inline-block font-semibold">
                {{ __('pubg.buy_uc_now') }}
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-gray-800 rounded-lg overflow-hidden border border-gray-700 hover:border-yellow-500/50 transition-all duration-300">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Order Info -->
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="font-semibold text-lg">{{ __('pubg.order_number', ['number' => '#' . str_pad($order->id, 6, '0', STR_PAD_LEFT)]) }}</h3>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                    @if($order->status == 'completed') bg-green-500/20 text-green-400 border border-green-500/30
                                    @elseif($order->status == 'processing') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                                    @elseif($order->status == 'pending') bg-blue-500/20 text-blue-400 border border-blue-500/30
                                    @elseif($order->status == 'failed') bg-red-500/20 text-red-400 border border-red-500/30
                                    @endif">
                                    {{ __('pubg.status_' . $order->status) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500 mb-1">{{ __('pubg.player_id') }}</p>
                                    <p class="font-mono font-semibold text-yellow-400">{{ $order->player_id }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 mb-1">{{ __('pubg.uc_amount') }}</p>
                                    <p class="font-semibold text-lg">{{ number_format($order->uc_amount) }} UC</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 mb-1">{{ __('pubg.price') }}</p>
                                    <p class="font-semibold text-lg text-green-400">${{ number_format($order->price, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 mb-1">{{ __('pubg.date') }}</p>
                                    <p class="font-semibold">{{ $order->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</p>
                                </div>
                            </div>

                            @if($order->transaction_id)
                            <div class="mt-4 p-3 bg-gray-900 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">{{ __('pubg.transaction_id') }}</p>
                                <p class="font-mono text-sm">{{ $order->transaction_id }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col gap-2">
                            @if($order->status == 'completed')
                                <button onclick="showReceipt({{ $order->id }})"
                                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-sm transition flex items-center gap-2 justify-center">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('pubg.download_receipt') }}
                                </button>
                            @endif

                            @if($order->status == 'pending')
                                <span class="px-4 py-2 bg-blue-500/20 text-blue-400 rounded text-sm text-center">
                                    {{ __('pubg.awaiting_payment') }}
                                </span>
                            @elseif($order->status == 'processing')
                                <span class="px-4 py-2 bg-yellow-500/20 text-yellow-400 rounded text-sm text-center animate-pulse">
                                    {{ __('pubg.processing_order') }}
                                </span>
                            @elseif($order->status == 'failed')
                                <a href="/pubg-uc" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm transition text-center">
                                    {{ __('pubg.retry_purchase') }}
                                </a>
                            @endif

                            <button onclick="toggleDetails({{ $order->id }})"
                                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded text-sm transition">
                                {{ __('pubg.view_details') }}
                            </button>
                        </div>
                    </div>

                    <!-- Expandable Details -->
                    <div id="details-{{ $order->id }}" class="hidden mt-6 pt-6 border-t border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-semibold mb-3 text-yellow-400">{{ __('pubg.order_timeline') }}</h4>
                                <div class="space-y-3">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-sm">{{ __('pubg.order_placed') }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>

                                    @if($order->status != 'pending')
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-sm">{{ __('pubg.payment_processed') }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    @if($order->status == 'completed')
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-sm text-green-400">{{ __('pubg.uc_delivered') }}</p>
                                            <p class="text-xs text-gray-500">{{ $order->updated_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h4 class="font-semibold mb-3 text-yellow-400">{{ __('pubg.purchase_summary') }}</h4>
                                <div class="bg-gray-900 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">{{ __('pubg.base_uc') }}</span>
                                        <span>{{ number_format($order->uc_amount) }} UC</span>
                                    </div>
                                    @if($order->uc_amount >= 325)
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">{{ __('pubg.bonus_uc') }}</span>
                                        <span class="text-green-400">
                                            @if($order->uc_amount == 325) +25 UC
                                            @elseif($order->uc_amount == 660) +60 UC
                                            @elseif($order->uc_amount == 1800) +200 UC
                                            @elseif($order->uc_amount == 3850) +450 UC
                                            @elseif($order->uc_amount == 8100) +1000 UC
                                            @endif
                                        </span>
                                    </div>
                                    @endif
                                    <div class="border-t border-gray-700 pt-2">
                                        <div class="flex justify-between font-semibold">
                                            <span>{{ __('pubg.total_price') }}</span>
                                            <span class="text-green-400">${{ number_format($order->price, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($order->status == 'failed')
                                <div class="mt-4 p-4 bg-red-900/20 border border-red-500/30 rounded-lg">
                                    <p class="text-sm text-red-400">
                                        <strong>{{ __('pubg.error') }}:</strong> {{ __('pubg.transaction_failed_message') }}
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->withQueryString()->links() }}
        </div>
    @endif
</div>

<!-- Receipt Modal (Hidden by default) -->
<div id="receiptModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">{{ __('pubg.receipt') }}</h3>
            <button onclick="closeReceipt()" class="text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        <div id="receiptContent" class="bg-gray-900 rounded p-4">
            <!-- Receipt content will be loaded here -->
        </div>
        <button onclick="printReceipt()" class="w-full mt-4 px-4 py-2 bg-yellow-500 text-gray-900 font-bold rounded hover:bg-yellow-400 transition">
            {{ __('pubg.print_receipt') }}
        </button>
    </div>
</div>

<script>
function toggleDetails(orderId) {
    const details = document.getElementById(`details-${orderId}`);
    details.classList.toggle('hidden');
}

function showReceipt(orderId) {
    const modal = document.getElementById('receiptModal');
    const content = document.getElementById('receiptContent');

    // Here you would normally fetch the receipt data via AJAX
    // For now, we'll show a placeholder
    content.innerHTML = `
        <div class="text-center mb-4">
            <h4 class="font-bold text-lg">PUBG UC Purchase Receipt</h4>
            <p class="text-sm text-gray-500">Order #${String(orderId).padStart(6, '0')}</p>
        </div>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span>Date:</span>
                <span>${new Date().toLocaleDateString()}</span>
            </div>
            <div class="flex justify-between">
                <span>Status:</span>
                <span class="text-green-400">Completed</span>
            </div>
            <div class="border-t border-gray-700 pt-2">
                <p class="text-center text-xs text-gray-500 mt-4">
                    Thank you for your purchase!
                </p>
            </div>
        </div>
    `;

    modal.classList.remove('hidden');
}

function closeReceipt() {
    const modal = document.getElementById('receiptModal');
    modal.classList.add('hidden');
}

function printReceipt() {
    window.print();
}
</script>
@endsection

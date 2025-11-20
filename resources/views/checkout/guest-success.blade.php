@extends('layouts.app')

@section('title', __('Order Successful'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Success Message -->
    <div class="bg-white rounded-2xl shadow-xl p-8 text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ __('Order Placed Successfully!') }}</h1>
        <p class="text-lg text-gray-600 mb-6">
            {{ __('Thank you for your purchase. Your order has been received and is being processed.') }}
        </p>
        
        <div class="bg-blue-50 rounded-lg p-6 mb-6">
            <p class="text-sm text-blue-800 mb-2">{{ __('Order Reference Number') }}</p>
            <p class="text-2xl font-bold text-blue-900">#{{ $order->reference_id }}</p>
        </div>
        
        <p class="text-gray-600">
            {{ __('A confirmation email has been sent to') }} 
            <span class="font-semibold text-gray-900">{{ $order->guest_email }}</span>
        </p>
    </div>
    
    <!-- Order Details -->
    <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Order Details') }}</h2>
        
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">{{ __('Customer Information') }}</h3>
                <p class="text-gray-900">{{ $order->guest_name }}</p>
                <p class="text-gray-600">{{ $order->guest_email }}</p>
                @if($order->guest_phone)
                    <p class="text-gray-600">{{ $order->guest_phone }}</p>
                @endif
            </div>
            
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">{{ __('Order Information') }}</h3>
                <p class="text-gray-900">{{ __('Order Date') }}: {{ $order->created_at->format('M d, Y') }}</p>
                <p class="text-gray-900">{{ __('Payment Method') }}: {{ ucfirst($order->payment_method) }}</p>
                <p class="text-gray-900">{{ __('Status') }}: <span class="font-semibold text-green-600">{{ ucfirst($order->status) }}</span></p>
            </div>
        </div>
        
        <!-- Order Items -->
        <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Items Ordered') }}</h3>
        <div class="space-y-4">
            @foreach($order->orderItems as $item)
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                    @if($item->product && $item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}" class="w-20 h-20 rounded-lg object-cover">
                    @else
                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">{{ $item->product_name }}</h4>
                        <p class="text-sm text-gray-600">{{ __('Quantity') }}: {{ $item->quantity }}</p>
                        <p class="text-sm font-semibold text-gray-700">
                            {{ app(\App\Services\CurrencyService::class)->formatPrice($item->selling_price) }} × {{ $item->quantity }}
                        </p>
                    </div>
                    
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900">
                            {{ app(\App\Services\CurrencyService::class)->formatPrice($item->total_price) }}
                        </p>
                        <p class="text-sm">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $item->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $item->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $item->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $item->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </p>
                    </div>
                </div>
                
                <!-- Show serials if delivered -->
                @if($item->status === 'delivered' && $item->serials)
                    <div class="ml-24 mt-2 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                        <p class="text-sm font-semibold text-green-800 mb-2">{{ __('Your Code(s)') }}:</p>
                        @if(is_array($item->serials))
                            @foreach($item->serials as $serial)
                                <div class="bg-white px-4 py-2 rounded font-mono text-sm text-gray-900 border border-green-200 mb-1">
                                    {{ $serial }}
                                </div>
                            @endforeach
                        @else
                            <div class="bg-white px-4 py-2 rounded font-mono text-sm text-gray-900 border border-green-200">
                                {{ $item->serials }}
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
        
        <!-- Order Total -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="space-y-2">
                <div class="flex justify-between text-gray-600">
                    <span>{{ __('Subtotal') }}</span>
                    <span>{{ app(\App\Services\CurrencyService::class)->formatPrice($order->subtotal) }}</span>
                </div>
                
                @if($order->vat_amount > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>{{ __('VAT') }}</span>
                        <span>{{ app(\App\Services\CurrencyService::class)->formatPrice($order->vat_amount) }}</span>
                    </div>
                @endif
                
                <div class="flex justify-between text-xl font-bold text-gray-900 pt-2 border-t border-gray-200">
                    <span>{{ __('Total') }}</span>
                    <span class="text-primary-blue">{{ app(\App\Services\CurrencyService::class)->formatPrice($order->total_amount) }}</span>
                </div>
                <p class="text-sm text-gray-500 text-right">{{ __('Paid in') }} {{ $order->currency }}</p>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="px-8 py-3 bg-primary-blue text-white rounded-lg font-semibold hover:bg-blue-600 transition-colors">
                {{ __('Continue Shopping') }}
            </a>
            
            <button onclick="window.print()" class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:border-primary-blue hover:text-primary-blue transition-colors">
                {{ __('Print Order') }}
            </button>
        </div>
        
        <p class="mt-6 text-sm text-gray-600">
            {{ __('Please save your order reference number for future inquiries.') }}
        </p>
        
        <p class="mt-2 text-sm text-gray-600">
            {{ __('Need help?') }} 
            <a href="mailto:support@example.com" class="text-primary-blue hover:underline">{{ __('Contact Support') }}</a>
        </p>
    </div>
</div>

<style>
@media print {
    nav, footer, button, .no-print {
        display: none !important;
    }
}
</style>
@endsection

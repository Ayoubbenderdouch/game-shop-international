@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div>
            <div class="bg-gray-800 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Order Summary</h2>

                @foreach($cartItems as $item)
                <div class="flex justify-between mb-3">
                    <div>
                        <p class="font-medium">{{ $item->product->title }}</p>
                        <p class="text-sm text-gray-400">Quantity: {{ $item->quantity }}</p>
                    </div>
                    <span>${{ number_format($item->product->price * $item->quantity, 2) }}</span>
                </div>
                @endforeach

                <div class="border-t border-gray-700 pt-3 mt-3">
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total:</span>
                        <span class="text-[#49baee]">${{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <form action="/checkout" method="POST" class="bg-gray-800 rounded-lg p-6">
                @csrf

                <h2 class="text-xl font-semibold mb-4">Payment Information</h2>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Card Number</label>
                    <input type="text"
                           placeholder="1234 5678 9012 3456"
                           class="w-full bg-gray-700 rounded px-4 py-2">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Expiry Date</label>
                        <input type="text"
                               placeholder="MM/YY"
                               class="w-full bg-gray-700 rounded px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">CVV</label>
                        <input type="text"
                               placeholder="123"
                               class="w-full bg-gray-700 rounded px-4 py-2">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Cardholder Name</label>
                    <input type="text"
                           placeholder="John Doe"
                           class="w-full bg-gray-700 rounded px-4 py-2">
                </div>

                <div class="bg-yellow-900/20 border border-yellow-600 rounded p-3 mb-4">
                    <p class="text-sm text-yellow-400">
                        This is a mock checkout. No real payment will be processed.
                    </p>
                </div>

                <button type="submit" class="w-full neon-button py-3 rounded font-semibold">
                    Complete Purchase
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

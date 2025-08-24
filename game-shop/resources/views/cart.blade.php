@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

    @if($cartItems->isEmpty())
        <div class="text-center py-16 bg-gray-800 rounded-lg">
            <p class="text-xl text-gray-400 mb-4">Your cart is empty</p>
            <a href="/shop" class="neon-button px-6 py-2 rounded inline-block">
                Continue Shopping
            </a>
        </div>
    @else
        <div class="bg-gray-800 rounded-lg p-6">
            @foreach($cartItems as $item)
            <div class="flex items-center justify-between border-b border-gray-700 pb-4 mb-4 last:border-0">
                <div class="flex items-center space-x-4">
                    @if($item->product->image_url)
                        <img src="{{ $item->product->image_url }}"
                             alt="{{ $item->product->title }}"
                             class="w-20 h-20 object-cover rounded">
                    @else
                        <div class="w-20 h-20 bg-gray-700 rounded flex items-center justify-center">
                            <span class="text-gray-500 text-xs">No Image</span>
                        </div>
                    @endif

                    <div>
                        <h3 class="font-semibold">{{ $item->product->title }}</h3>
                        <p class="text-[#49baee]">${{ $item->product->price }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <form action="/cart/{{ $item->id }}" method="POST" class="flex items-center space-x-2">
                        @csrf
                        @method('PATCH')
                        <button type="button"
                                onclick="this.parentElement.querySelector('input').stepDown(); this.parentElement.submit()"
                                class="w-8 h-8 rounded bg-gray-700 hover:bg-gray-600 transition">
                            -
                        </button>
                        <input type="number"
                               name="quantity"
                               value="{{ $item->quantity }}"
                               min="1"
                               class="w-16 text-center bg-gray-700 rounded">
                        <button type="button"
                                onclick="this.parentElement.querySelector('input').stepUp(); this.parentElement.submit()"
                                class="w-8 h-8 rounded bg-gray-700 hover:bg-gray-600 transition">
                            +
                        </button>
                    </form>

                    <span class="font-semibold">
                        ${{ number_format($item->product->price * $item->quantity, 2) }}
                    </span>

                    <form action="/cart/{{ $item->id }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-400 transition">
                            Remove
                        </button>
                    </form>
                </div>
            </div>
            @endforeach

            <div class="mt-6 pt-6 border-t border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xl font-semibold">Total:</span>
                    <span class="text-2xl font-bold text-[#49baee]">
                        ${{ number_format($total, 2) }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <a href="/shop" class="text-gray-400 hover:text-white transition">
                        Continue Shopping
                    </a>
                    <a href="/checkout" class="neon-button px-8 py-2 rounded">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

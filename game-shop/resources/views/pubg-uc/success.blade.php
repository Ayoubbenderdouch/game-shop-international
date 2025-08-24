@extends('layouts.app')

@section('title', 'UC Purchase Successful')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <div class="bg-gray-800 rounded-lg p-8 border-2 border-green-500">
        <div class="text-6xl mb-4">✅</div>

        <h1 class="text-3xl font-bold mb-4 text-green-500">Purchase Successful!</h1>

        <p class="text-xl mb-6">
            {{ $order->uc_amount }} UC has been added to your PUBG account
        </p>

        <div class="bg-gray-900 rounded p-4 mb-6">
            <p class="text-sm text-gray-400 mb-2">Transaction Details</p>
            <p><strong>Player ID:</strong> {{ $order->player_id }}</p>
            <p><strong>UC Amount:</strong> {{ $order->uc_amount }}</p>
            <p><strong>Transaction ID:</strong> {{ $order->transaction_id }}</p>
            <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}</p>
        </div>

        <div class="flex justify-center space-x-4">
            <a href="/pubg-uc" class="neon-button px-6 py-2 rounded">
                Buy More UC
            </a>
            <a href="/pubg-uc/orders" class="bg-gray-700 px-6 py-2 rounded hover:bg-gray-600 transition">
                View UC Orders
            </a>
        </div>
    </div>
</div>
@endsection

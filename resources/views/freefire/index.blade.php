@extends('layouts.app')

@section('title', 'Free Fire Diamonds Top-Up')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-4 text-orange-400">Free Fire Diamonds Top-Up</h1>
        <p class="text-xl text-gray-300">Instant Diamond delivery to your account</p>
    </div>

    <!-- How to Redeem Section -->
    <div class="bg-gray-800 rounded-lg p-8 mb-8 border-2 border-orange-500">
        <h2 class="text-2xl font-bold mb-6 text-orange-400">How to Redeem Free Fire Card</h2>
        <ol class="list-decimal list-inside space-y-3 text-gray-300">
            <li>Enter the link and choose the game FREE FIRE: <a href="https://shop.garena.sg/app" target="_blank" class="text-orange-400 hover:underline">shop.garena.sg/app</a></li>
            <li>Choose the shipping method via the player ID (PLAYER ID)</li>
            <li>Enter your PLAYER ID and press LOG IN</li>
            <li>Select the Garena PPC option, then enter the code and press Confirm</li>
        </ol>

        <div class="mt-6 p-4 bg-gray-900 rounded">
            <h3 class="font-semibold text-orange-400 mb-2">How to find Free Fire Player ID:</h3>
            <ol class="list-decimal list-inside space-y-2 text-sm text-gray-400">
                <li>Use your account to log into the game</li>
                <li>Click your avatar on the top-left corner</li>
                <li>Your Free Fire Player ID will be displayed</li>
            </ol>
        </div>
    </div>

    <form action="/freefire/charge" method="POST" class="bg-gray-800 rounded-lg p-8">
        @csrf

        <div class="mb-6">
            <label for="player_id" class="block text-sm font-medium mb-2">Free Fire Player ID</label>
            <input type="text"
                   id="player_id"
                   name="player_id"
                   required
                   placeholder="Enter your Player ID"
                   class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            <p class="text-sm text-gray-400 mt-1">You can find your Player ID in your Free Fire profile</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-4">Select Diamond Package</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($packages as $package)
                <label class="relative">
                    <input type="radio"
                           name="diamond_amount"
                           value="{{ $package['diamonds'] }}"
                           required
                           class="peer absolute opacity-0">
                    <div class="bg-gray-700 rounded-lg p-4 cursor-pointer transition
                                peer-checked:border-2 peer-checked:border-orange-500
                                hover:bg-gray-600">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-orange-400 mb-1">
                                💎 {{ $package['diamonds'] }}
                            </div>
                            @if($package['bonus'] > 0)
                                <div class="text-sm text-green-400 mb-2">
                                    +{{ $package['bonus'] }} Bonus
                                </div>
                            @endif
                            <div class="text-xl font-bold text-white">
                                ${{ $package['price'] }}
                            </div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <button type="submit" class="w-full neon-button-orange py-3 rounded-lg font-semibold text-lg">
            Proceed to Payment
        </button>
    </form>

    <div class="mt-8 bg-gray-800 rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-3 text-orange-400">Important Notes:</h3>
        <ul class="list-disc list-inside space-y-1 text-sm text-gray-400">
            <li>Make sure your Player ID is correct before purchasing</li>
            <li>Diamonds will be added within 5 minutes after successful payment</li>
            <li>No refunds for incorrect Player ID</li>
            <li>Contact support if you face any issues</li>
        </ul>
    </div>
</div>
@endsection

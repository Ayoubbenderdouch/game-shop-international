@extends('layouts.app')

@section('title', 'PUBG UC Top-Up')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-4 text-yellow-400">PUBG UC Top-Up</h1>
        <p class="text-xl text-gray-300">Instant UC delivery to your account</p>
    </div>

    <div class="bg-gray-800 rounded-lg p-8 mb-8 border-2 border-yellow-500">
        <h2 class="text-2xl font-bold mb-6">How it works</h2>
        <ol class="list-decimal list-inside space-y-2 text-gray-300">
            <li>Enter your PUBG Player ID</li>
            <li>Select UC package</li>
            <li>Complete payment</li>
            <li>UC will be added to your account instantly!</li>
        </ol>
    </div>

    <form action="/pubg-uc/charge" method="POST" class="bg-gray-800 rounded-lg p-8">
        @csrf

        <div class="mb-6">
            <label for="player_id" class="block text-sm font-medium mb-2">PUBG Player ID</label>
            <input type="text"
                   id="player_id"
                   name="player_id"
                   required
                   placeholder="Enter your Player ID"
                   class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500">
            <p class="text-sm text-gray-400 mt-1">You can find your Player ID in your PUBG profile</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-4">Select UC Package</label>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($packages as $package)
                <label class="relative">
                    <input type="radio"
                           name="uc_amount"
                           value="{{ $package['uc'] }}"
                           required
                           class="peer absolute opacity-0">
                    <div class="bg-gray-700 rounded-lg p-4 cursor-pointer transition
                                peer-checked:border-2 peer-checked:border-yellow-500
                                hover:bg-gray-600">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-400 mb-1">
                                {{ $package['uc'] }} UC
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

        <button type="submit" class="w-full neon-button py-3 rounded-lg font-semibold text-lg">
            Proceed to Payment
        </button>
    </form>

    <div class="mt-8 bg-gray-800 rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-3">Important Notes:</h3>
        <ul class="list-disc list-inside space-y-1 text-sm text-gray-400">
            <li>Make sure your Player ID is correct before purchasing</li>
            <li>UC will be added within 5 minutes after successful payment</li>
            <li>No refunds for incorrect Player ID</li>
            <li>Contact support if you face any issues</li>
        </ul>
    </div>
</div>
@endsection

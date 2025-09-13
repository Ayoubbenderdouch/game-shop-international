<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @auth
                        <h3 class="text-lg font-semibold mb-4">Welcome back, {{ Auth::user()->name }}!</h3>

                        @if(Auth::user()->isAdmin())
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                                <p class="text-blue-700">
                                    You have admin privileges.
                                    <a href="{{ route('admin.dashboard') }}" class="underline font-semibold">Go to Admin Panel</a>
                                </p>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700">Your Orders</h4>
                                <p class="text-2xl font-bold text-gray-900">
                                    @php
                                        try {
                                            $orderCount = Auth::user()->orders()->count();
                                        } catch (\Exception $e) {
                                            $orderCount = 0;
                                        }
                                    @endphp
                                    {{ $orderCount }}
                                </p>
                                <a href="{{ route('orders.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View all orders →</a>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700">Cart Items</h4>
                                <p class="text-2xl font-bold text-gray-900">
                                    @php
                                        try {
                                            $cartCount = Auth::user()->cartItems()->count();
                                        } catch (\Exception $e) {
                                            $cartCount = 0;
                                        }
                                    @endphp
                                    {{ $cartCount }}
                                </p>
                                <a href="{{ route('cart.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View cart →</a>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700">Favorites</h4>
                                <p class="text-2xl font-bold text-gray-900">
                                    @php
                                        try {
                                            $favCount = Auth::user()->favorites()->count();
                                        } catch (\Exception $e) {
                                            $favCount = 0;
                                        }
                                    @endphp
                                    {{ $favCount }}
                                </p>
                                <a href="{{ route('favorites.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View favorites →</a>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-4">
                            <a href="{{ route('shop') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Browse Shop
                            </a>
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Edit Profile
                            </a>
                        </div>
                    @else
                        <p>You are not logged in.</p>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

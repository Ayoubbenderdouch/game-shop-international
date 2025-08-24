@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-gray-800 rounded-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

        <form method="POST" action="/login">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium mb-2">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#49baee]">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium mb-2">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       required
                       class="w-full bg-gray-700 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#49baee]">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-sm">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full neon-button py-2 rounded font-semibold">
                Login
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-400">
                Don't have an account?
                <a href="/register" class="text-[#49baee] hover:underline">Register</a>
            </p>
        </div>
    </div>
</div>
@endsection

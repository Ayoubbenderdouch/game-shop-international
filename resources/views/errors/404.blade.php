@extends('layouts.app')

@section('title', '404 - Page Not Found')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8 text-center">
        <div class="mb-6">
            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h2>
        <p class="text-gray-600 mb-8">Sorry, the page you're looking for doesn't exist.</p>

        <div class="space-y-3">
            <a href="{{ route('home') }}" class="block w-full px-6 py-3 bg-gradient-to-r from-[#49b8ef] to-[#3da2d4] text-white font-bold rounded-lg hover:shadow-lg transition-all duration-300">
                Go to Homepage
            </a>
            <a href="{{ route('shop') }}" class="block w-full px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-all duration-300">
                Browse Shop
            </a>
        </div>
    </div>
</div>
@endsection

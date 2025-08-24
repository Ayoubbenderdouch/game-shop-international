<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Game Shop')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a2e 100%);
            color: #ffffff;
            min-height: 100vh;
        }
        .neon-border {
            border: 1px solid #49baee;
            box-shadow: 0 0 10px rgba(73, 186, 238, 0.5);
        }
        .neon-button {
            background: linear-gradient(135deg, #49baee 0%, #38a8dc 100%);
            transition: all 0.3s;
        }
        .neon-button:hover {
            box-shadow: 0 0 20px rgba(73, 186, 238, 0.8);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="antialiased">
    <nav class="bg-gray-900 border-b border-gray-800">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="/" class="text-2xl font-bold text-[#49baee]">Game Shop</a>

                <div class="flex items-center space-x-6">
                    <a href="/" class="hover:text-[#49baee] transition">Home</a>
                    <a href="/shop" class="hover:text-[#49baee] transition">Shop</a>
                    <a href="/pubg-uc" class="hover:text-[#49baee] transition">PUBG UC</a>

                    @auth
                        <a href="/cart" class="hover:text-[#49baee] transition">Cart</a>
                        <a href="/orders" class="hover:text-[#49baee] transition">Orders</a>

                        @if(auth()->user()->is_admin)
                            <a href="/admin" class="hover:text-[#49baee] transition">Admin</a>
                        @endif

                        <form action="/logout" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="hover:text-[#49baee] transition">Logout</button>
                        </form>
                    @else
                        <a href="/login" class="hover:text-[#49baee] transition">Login</a>
                        <a href="/register" class="hover:text-[#49baee] transition">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-8">
        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500 text-green-500 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-500/20 border border-red-500 text-red-500 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-gray-900 border-t border-gray-800 mt-20">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center text-gray-400">
                <p>&copy; 2024 Game Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

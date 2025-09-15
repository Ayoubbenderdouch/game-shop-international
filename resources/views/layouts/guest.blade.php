<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gaming Store') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#49b8ef',
                        'primary-black': '#000000',
                        'primary-border': '#23262B',
                        'primary-border-secondary': '#3C3E42'
                    },
                    fontFamily: {
                        'urbanist': ['Urbanist', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: "Urbanist", sans-serif;
        }

        /* Custom animations */
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 15s ease infinite;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #49b8ef;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #3da2d4;
        }

        /* Floating animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#0b0e13] text-[#e5e7eb] min-h-screen">
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background effects -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0b0e13] via-[#0b0e13] to-black opacity-90"></div>
            <div class="absolute top-1/4 right-1/4 w-96 h-96 bg-[#49b8ef]/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 left-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-3xl"></div>

            <!-- Grid pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="40" height="40" xmlns="http://www.w3.org/2000/svg"%3E%3Cdefs%3E%3Cpattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"%3E%3Cpath d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(73,184,239,0.05)" stroke-width="1"/%3E%3C/pattern%3E%3C/defs%3E%3Crect width="100%25" height="100%25" fill="url(%23grid)"/%3E%3C/svg%3E')]"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 w-full">
            {{ $slot }}
        </div>
    </div>
</body>
</html>

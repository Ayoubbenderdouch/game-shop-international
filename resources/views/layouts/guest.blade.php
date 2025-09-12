<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
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
                width: 10px;
                height: 10px;
            }

            ::-webkit-scrollbar-track {
                background: rgb(15 23 42);
            }

            ::-webkit-scrollbar-thumb {
                background: linear-gradient(to bottom, rgb(6 182 212), rgb(147 51 234));
                border-radius: 5px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(to bottom, rgb(8 145 178), rgb(126 34 206));
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        {{ $slot }}
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TrainEvents')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col overflow-hidden">
<!-- Desktop Header -->
<header class="hidden md:flex items-center justify-between p-4 border-b bg-white z-10">
    <div class="flex items-center gap-2">
        <div class="h-8 w-8 rounded-md bg-blue-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
        </div>
        <span class="text-xl font-bold text-blue-600">TrainEvents</span>
    </div>
    <div class="flex items-center gap-4">
        <a href="#" class="text-sm font-medium hover:text-blue-600 transition-colors">About</a>
        <a href="#" class="text-sm font-medium hover:text-blue-600 transition-colors">Browse</a>
        <a href="#" class="text-sm font-medium hover:text-blue-600 transition-colors">Contact</a>
        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 hover:text-blue-600">
            Login
        </a>
        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
            Create Campaign
        </a>
    </div>
</header>

<main class="flex-1">
    @yield('content')
</main>

<!-- Footer -->
<footer class="border-t py-8 px-4 bg-white">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="h-8 w-8 rounded-md bg-blue-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-blue-600">TrainEvents</span>
                </div>
                <p class="text-sm text-gray-600">
                    The platform for discovering and creating amazing training programs and events.
                </p>
            </div>
            <div>
                <h3 class="font-medium mb-3 text-blue-800">Platform</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Browse</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Create Campaign</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Pricing</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-medium mb-3 text-blue-800">Company</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">About</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Careers</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Contact</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-medium mb-3 text-blue-800">Legal</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Terms</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Privacy</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Cookies</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t text-center text-sm text-gray-600">
            &copy; {{ date('Y') }} TrainEvents. All rights reserved.
        </div>
    </div>
</footer>

@include('partials.mobile-navigation')
</body>
</html>

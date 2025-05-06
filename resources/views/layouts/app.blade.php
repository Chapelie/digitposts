<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Digitposts')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col overflow-auto">
<!-- Desktop Header -->
<header class="hidden md:flex items-center justify-between p-4 border-b bg-white z-10">
    <div class="flex items-center gap-2">
        <div class="h-8 w-8 rounded-md bg-blue-600 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
        </div>
        <span class="text-xl font-bold text-blue-600">Digitposts</span>
    </div>
    <div class="flex items-center gap-4">
        <a href="#" class="text-sm font-medium hover:text-blue-600 transition-colors">À propos</a>
        <a href="#" class="text-sm font-medium hover:text-blue-600 transition-colors">Explorer</a>
        <a href="#" class="text-sm font-medium hover:text-blue-600 transition-colors">Contact</a>
        @guest
            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 hover:text-blue-600">
                Connexion
            </a>
            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                Créer une campagne
            </a>
        @endguest
        @auth
            <!-- Menu utilisateur connecté -->
            <div class="relative">
                <button class="flex items-center space-x-2 focus:outline-none">
                    <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20 hidden">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        @endauth
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
                    La plateforme pour découvrir et créer des programmes de formation et événements exceptionnels.
                </p>
            </div>
            <div>
                <h3 class="font-medium mb-3 text-blue-800">Plateforme</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Explorer</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Créer une campagne</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Tarifs</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-medium mb-3 text-blue-800">Entreprise</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">À propos</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Carrières</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Contact</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-medium mb-3 text-blue-800">Légal</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Conditions</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Confidentialité</a></li>
                    <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Cookies</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t text-center text-sm text-gray-600">
            &copy; {{ date('Y') }} TrainEvents. Tous droits réservés.
        </div>
    </div>
</footer>

@include('partials.mobile-navigation')
</body>
</html>

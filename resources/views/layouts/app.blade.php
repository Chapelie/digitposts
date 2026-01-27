<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @include('components.seo-meta', [
        'title' => $seoTitle ?? 'DigitPosts - Formations & Événements Professionnels au Burkina Faso',
        'description' => $seoDescription ?? 'Découvrez les meilleures formations et événements professionnels au Burkina Faso. Inscrivez-vous facilement et développez vos compétences.',
        'keywords' => $seoKeywords ?? 'formations, événements, Burkina Faso, développement professionnel, formations gratuites, événements professionnels',
        'image' => $seoImage ?? null,
        'url' => $seoUrl ?? url()->current(),
        'type' => $seoType ?? 'website',
        'publishedTime' => $seoPublishedTime ?? null,
        'modifiedTime' => $seoModifiedTime ?? null,
        'structuredData' => $seoStructuredData ?? null,
    ])
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-image: url('{{ asset('asset/image1_large.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        
        /* Overlay plus léger pour mieux voir l'image */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
            z-index: -1;
        }
        
        /* Assurer que le contenu reste lisible */
        .bg-white {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }
        
        /* Améliorer la lisibilité des sections */
        .bg-gray-50 {
            background-color: rgba(249, 250, 251, 0.9) !important;
        }
        
        /* Header avec effet de verre */
        header {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }
        
        /* Footer avec effet de verre */
        footer {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col overflow-auto">
<!-- Desktop Header -->
<header class="hidden md:flex items-center justify-between p-4 border-b bg-white z-10">
    <a href="{{ url('/') }}" class="flex items-center gap-2">
        <img src="{{ asset('asset/logo.jpg') }}" alt="DigitPosts" class="h-8 w-auto object-contain">
    </a>
    <div class="flex items-center gap-4">
        @auth
            <a href="{{ route('user.favorites') }}" class="text-sm font-medium hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                Mes Favoris
            </a>
            <a href="{{ route('user.registrations') }}" class="text-sm font-medium hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Mes Inscriptions
            </a>
        @else
            <a href="#activities" class="text-sm font-medium hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Découvrir
            </a>
            <a href="#filter" class="text-sm font-medium hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"></polygon>
                </svg>
                Filtrer
            </a>
        @endauth
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
                <a href="{{ url('/') }}" class="inline-block mb-4">
                    <img src="{{ asset('asset/logo.jpg') }}" alt="DigitPosts" class="h-8 w-auto object-contain">
                </a>
                <p class="text-sm text-gray-600">
                Plateforme dédiée à la diffusion d'information sur les formations et évènements au Burkina Faso.
                Que vous soyez un Professionnels en quète d'opportunité, un étudiant à la recherche de formations,
                ou une organisation souhaitant partager ses évènements, Diginov vous connecte aux informations
                essentielles pour votre développement.               
             </p>
            </div>
            <div>
                <h3 class="font-medium mb-3 text-blue-800">Plateforme</h3>
                <ul class="space-y-2">
                    <li><a href="#activities" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Découvrir les activités</a></li>
                    <li><a href="{{ route('campaigns.create') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Créer une activité</a></li>
                    <li><a href="#filter" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Filtrer par catégorie</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-medium mb-3 text-blue-800">Utilisateur</h3>
                <ul class="space-y-2">
                    @auth
                        <li><a href="{{ route('user.favorites') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Mes Favoris</a></li>
                        <li><a href="{{ route('user.registrations') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Mes Inscriptions</a></li>
                        <li><a href="{{ route('user.dashboard') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Mon Dashboard</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Se connecter</a></li>
                        <li><a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">S'inscrire</a></li>
                        <li><a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Contact</a></li>
                    @endauth
                </ul>
            </div>
            <div>
                <h3 class="font-medium mb-3 text-blue-800">Légal</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('legal.terms') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Conditions d'utilisation</a></li>
                    <li><a href="{{ route('legal.privacy') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Confidentialité</a></li>
                    <li><a href="{{ route('legal.cookies') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Cookies</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 pt-8 border-t text-center text-sm text-gray-600">
            &copy; {{ date('Y') }} DigiPost. Tous droits réservés.
        </div>
    </div>
</footer>

@include('partials.mobile-navigation')
</body>
</html>

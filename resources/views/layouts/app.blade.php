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
    <a href="{{ url('/') }}" class="flex items-center gap-3">
        <img src="{{ asset('asset/logo.jpg') }}" alt="DigitPosts" class="h-8 w-auto object-contain">
        <span class="text-xl font-bold text-blue-600">DigitPosts</span>
    </a>
    <div class="flex items-center gap-4">
        @guest
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
        @endguest
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
            <!-- Icône profil (collapse menu) -->
            <div class="relative">
                <button id="user-menu-button" type="button" aria-haspopup="true" aria-expanded="false"
                        class="flex items-center gap-2 focus:outline-none hover:opacity-90 transition-opacity">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-semibold text-sm">
                        {{ strtoupper(substr(Auth::user()->firstname ?? 'U', 0, 1)) }}{{ strtoupper(substr(Auth::user()->lastname ?? '', 0, 1)) }}
                    </span>
                </button>
                <!-- Dropdown menu -->
                <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20 hidden">
                    <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profil
                    </a>
                    <a href="{{ route('subscriptions.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Abonnement
                    </a>
                    <a href="{{ route('user.favorites') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Mes Favoris
                    </a>
                    <a href="{{ route('user.registrations') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Mes Inscriptions
                    </a>
                    <a href="{{ route('dashboard.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Paramètres
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
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
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 mb-4">
                    <img src="{{ asset('asset/logo.jpg') }}" alt="DigitPosts" class="h-8 w-auto object-contain">
                    <span class="text-xl font-bold text-blue-600">DigitPosts</span>
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

<script>
    // Activer le menu dropdown utilisateur
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('user-menu-button');
        const menu = document.getElementById('user-menu');
        
        if (menuButton && menu) {
            menuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
                menuButton.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
            });
            
            // Fermer le menu en cliquant ailleurs
            document.addEventListener('click', function(e) {
                if (!menuButton.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                    menuButton.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
</script>
</body>
</html>

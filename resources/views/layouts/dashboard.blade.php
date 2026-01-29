<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @include('components.seo-meta', [
        'title' => $seoTitle ?? 'Dashboard - DigitPosts',
        'description' => $seoDescription ?? 'Tableau de bord DigitPosts - Gérez vos formations, événements et inscriptions',
        'keywords' => $seoKeywords ?? 'dashboard, tableau de bord, DigitPosts',
        'image' => $seoImage ?? null,
        'url' => $seoUrl ?? url()->current(),
        'type' => $seoType ?? 'website',
        'structuredData' => $seoStructuredData ?? null,
    ])
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #f9fafb;
        }
        
        /* Sidebar styling */
        #sidebar {
            background-color: #ffffff;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
        }
        
        /* Main content area */
        main {
            background-color: #f9fafb;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row bg-gray-50">
<!-- Mobile Header -->
<div class="md:hidden flex items-center justify-between p-4 border-b bg-white shadow-sm sticky top-0 z-40">
    <div class="flex items-center gap-2">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ asset('asset/logo.jpg') }}" alt="DigitPosts" class="h-8 w-auto object-contain">
            <span class="text-xl font-bold text-blue-600">DigitPosts</span>
        </a>
    </div>
    <div class="flex items-center gap-2">
        <!-- Icône Profil (collapse menu) -->
        <div class="relative">
            <button id="dash-user-menu-button" type="button" aria-haspopup="true" aria-expanded="false"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-semibold border border-blue-200">
                {{ strtoupper(substr(Auth::user()->firstname ?? 'U', 0, 1)) }}{{ strtoupper(substr(Auth::user()->lastname ?? '', 0, 1)) }}
            </button>
            <div id="dash-user-menu" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                <a href="{{ route('user.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                <a href="{{ route('subscriptions.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Abonnement</a>
                <a href="{{ route('user.favorites') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mes Favoris</a>
                <a href="{{ route('user.registrations') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mes Inscriptions</a>
                <a href="{{ route('dashboard.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-blue-700 hover:bg-blue-50 font-medium">Administration</a>
                @endif
                <div class="my-1 border-t border-gray-100"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Déconnexion</button>
                </form>
            </div>
        </div>

        <button type="button" class="p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 bg-white border border-gray-200 shadow-sm transition-all duration-200" onclick="toggleSidebar()" aria-label="Toggle menu">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" x2="20" y1="12" y2="12"/>
                <line x1="4" x2="20" y1="6" y2="6"/>
                <line x1="4" x2="20" y1="18" y2="18"/>
            </svg>
        </button>
    </div>
</div>

<!-- Sidebar for desktop -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 -translate-x-full shadow-lg">
    <div class="flex flex-col h-full">
        <div class="p-4 border-b border-gray-200 bg-white">
            <a href="{{ route('home') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                <img src="{{ asset('asset/logo.jpg') }}" alt="DigitPosts" class="h-8 w-auto object-contain">
                <span class="text-xl font-bold text-blue-600">DigitPosts</span>
            </a>
        </div>

        <div class="flex-1 py-6 px-4 space-y-1 overflow-y-auto bg-white">
            @php
                $navItems = [
                    [
                        'title' => 'Dashboard',
                        'href' => route('user.dashboard'),
                        'icon' => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 22V12h6v10',
                    ],
                    [
                        'title' => 'Mes Inscriptions',
                        'href' => route('user.registrations'),
                        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    [
                        'title' => 'Mes Favoris',
                        'href' => route('user.favorites'),
                        'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                    ],
                    [
                        'title' => 'Mes Campagnes',
                        'href' => route('dashboard.campaigns'),
                        'icon' => 'M8 2v4 M16 2v4 M3.5 10h17 M21 10v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8',
                    ],
                    [
                        'title' => 'Inscriptions',
                        'href' => route('dashboard.registrations'),
                        'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M22 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75',
                    ],
                    [
                        'title' => 'Créer une Campagne',
                        'href' => route('dashboard.campaigns.create'),
                        'icon' => 'M12 5v14 M5 12h14 M12 5v14 M5 12h14',
                    ],
                    [
                        'title' => 'Paramètres',
                        'href' => route('dashboard.settings'),
                        'icon' => 'M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z M9 12a3 3 0 1 0 6 0 3 3 0 0 0-6 0',
                    ],
                ];

                // Ajouter le lien Admin si l'utilisateur est administrateur
                if (Auth::user()->isAdmin()) {
                    $navItems[] = [
                        'title' => 'Administration',
                        'href' => route('admin.dashboard'),
                        'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
                        'admin' => true,
                    ];
                }
            @endphp

            @foreach($navItems as $item)
                @php
                    $isActive = false;
                    $currentPath = request()->path();
                    $itemPath = parse_url($item['href'], PHP_URL_PATH);
                    
                    // Vérifier si la route actuelle correspond
                    if ($currentPath === ltrim($itemPath, '/')) {
                        $isActive = true;
                    } elseif (str_starts_with($currentPath, ltrim($itemPath, '/'))) {
                        $isActive = true;
                    }
                @endphp
                <a href="{{ $item['href'] }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200 {{ $isActive ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-600 font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 {{ $isActive ? 'text-blue-600' : '' }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['title'] }}
                </a>
            @endforeach
        </div>

        <div class="p-4 border-t border-gray-200 bg-white">
            <button id="sidebar-user-menu-button" type="button" class="w-full text-left px-3 py-2 rounded-md hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-2">
                    <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-blue-700 font-semibold text-sm">
                            {{ strtoupper(substr(Auth::user()->firstname ?? 'U', 0, 1)) }}{{ strtoupper(substr(Auth::user()->lastname ?? '', 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ Auth::user()->firstname }} {{ Auth::user()->lastname }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </button>
            <div id="sidebar-user-menu" class="mt-2 hidden">
                <a href="{{ route('user.profile') }}" class="block px-3 py-2 text-sm text-gray-700 rounded-md hover:bg-gray-50">Profil</a>
                <a href="{{ route('subscriptions.index') }}" class="block px-3 py-2 text-sm text-gray-700 rounded-md hover:bg-gray-50">Abonnement</a>
                <a href="{{ route('user.favorites') }}" class="block px-3 py-2 text-sm text-gray-700 rounded-md hover:bg-gray-50">Mes Favoris</a>
                <a href="{{ route('user.registrations') }}" class="block px-3 py-2 text-sm text-gray-700 rounded-md hover:bg-gray-50">Mes Inscriptions</a>
                <a href="{{ route('dashboard.settings') }}" class="block px-3 py-2 text-sm text-gray-700 rounded-md hover:bg-gray-50">Paramètres</a>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-sm text-blue-700 font-medium rounded-md hover:bg-blue-50">Administration</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 rounded-md transition-colors border border-red-200">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="flex-1 md:ml-64 flex flex-col min-h-screen">
    <main class="flex-1 p-4 md:p-6 lg:p-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            @yield('content')
        </div>
    </main>
</div>

<!-- Mobile Navigation -->
{{--@include('components.mobile-navigation')--}}

<!-- Backdrop for mobile sidebar -->
<div id="sidebarBackdrop" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40 md:hidden transition-opacity duration-300" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const body = document.body;

        if (sidebar.classList.contains('-translate-x-full')) {
            // Ouvrir le sidebar
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            backdrop.classList.remove('hidden');
            body.style.overflow = 'hidden'; // Empêcher le scroll du body
        } else {
            // Fermer le sidebar
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            body.style.overflow = ''; // Restaurer le scroll du body
        }
    }

    // Fermer le sidebar quand on clique sur un lien
    document.addEventListener('DOMContentLoaded', function() {
        // Dropdown profil (mobile header)
        const dashMenuBtn = document.getElementById('dash-user-menu-button');
        const dashMenu = document.getElementById('dash-user-menu');
        if (dashMenuBtn && dashMenu) {
            dashMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                dashMenu.classList.toggle('hidden');
                dashMenuBtn.setAttribute('aria-expanded', dashMenu.classList.contains('hidden') ? 'false' : 'true');
            });
            document.addEventListener('click', function(e) {
                if (!dashMenuBtn.contains(e.target) && !dashMenu.contains(e.target)) {
                    dashMenu.classList.add('hidden');
                    dashMenuBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }

        // Collapse profil (sidebar footer)
        const sidebarUserBtn = document.getElementById('sidebar-user-menu-button');
        const sidebarUserMenu = document.getElementById('sidebar-user-menu');
        if (sidebarUserBtn && sidebarUserMenu) {
            sidebarUserBtn.addEventListener('click', function(e) {
                e.preventDefault();
                sidebarUserMenu.classList.toggle('hidden');
            });
        }

        const sidebarLinks = document.querySelectorAll('#sidebar a');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const body = document.body;

        sidebarLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Fermer le sidebar sur mobile
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('translate-x-0');
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                    body.style.overflow = '';
                }
            });
        });

        // Fermer le sidebar quand on redimensionne l'écran
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                backdrop.classList.add('hidden');
                body.style.overflow = '';
            }
        });
    });
</script>
</body>
</html>

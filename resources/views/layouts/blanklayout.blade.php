<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @include('components.seo-meta', [
        'title' => $seoTitle ?? (@yield('title', 'DigitPosts')),
        'description' => $seoDescription ?? 'Plateforme de formations et événements professionnels au Burkina Faso',
        'keywords' => $seoKeywords ?? 'formations, événements, Burkina Faso',
        'image' => $seoImage ?? null,
        'url' => $seoUrl ?? url()->current(),
        'type' => $seoType ?? 'website',
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
            background: rgba(0, 0, 0, 0.15);
            z-index: -1;
        }
        
        /* Assurer que le contenu reste lisible */
        .bg-gray-50 {
            background-color: rgba(249, 250, 251, 0.9) !important;
        }
        
        /* Améliorer la lisibilité des cartes */
        .bg-white {
            background-color: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50">
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <div class="w-full sm:max-w-md px-6 py-4">
        <!-- Logo/Header -->
        <div class="flex justify-center mb-8">
            <a href="{{ url('/') }}">
{{--                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />--}}
            </a>
        </div>

        <!-- Page Content -->
        @yield('content')
    </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-W2W25DT5');</script>
<!-- End Google Tag Manager -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('asset/logo.jpg') }}" type="image/jpeg" sizes="32x32">
    
    @include('components.seo-meta', [
        'title' => $seoTitle ?? 'DigitPosts',
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
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W2W25DT5"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <div class="w-full sm:max-w-md px-6 py-4">
        <!-- Logo/Header -->
        <div class="flex justify-center mb-8">
            <a href="{{ url('/') }}" class="flex flex-col items-center gap-3">
                <img src="{{ asset('asset/logo.jpg') }}" alt="DigitPosts" class="h-12 w-auto object-contain">
                <span class="text-2xl font-bold text-blue-600">DigitPosts</span>
            </a>
        </div>

        <!-- Page Content -->
        @yield('content')
    </div>
</div>
</body>
</html>

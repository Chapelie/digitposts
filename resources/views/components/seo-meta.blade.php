@props([
    'title' => 'DigitPosts - Formations & Événements Professionnels au Burkina Faso',
    'description' => 'Découvrez les meilleures formations et événements professionnels au Burkina Faso. Inscrivez-vous facilement et développez vos compétences.',
    'keywords' => 'formations, événements, Burkina Faso, développement professionnel, formations gratuites, événements professionnels',
    'image' => null,
    'url' => null,
    'type' => 'website',
    'author' => 'DigitPosts',
    'publishedTime' => null,
    'modifiedTime' => null,
    'structuredData' => null,
])

@php
    $siteName = 'DigitPosts';
    $siteUrl = config('app.url');
    $currentUrl = $url ?? url()->current();
    $ogImage = $image ?? asset('asset/image1_large.jpg');
    $fullTitle = strlen($title) > 60 ? $title : $title . ' - ' . $siteName;
@endphp

<!-- Primary Meta Tags -->
<title>{{ $fullTitle }}</title>
<meta name="title" content="{{ $fullTitle }}">
<meta name="description" content="{{ Str::limit($description, 160) }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="{{ $author }}">
<meta name="robots" content="index, follow">
<meta name="language" content="French">
<meta name="revisit-after" content="7 days">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $currentUrl }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ Str::limit($description, 200) }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="fr_FR">

@if($publishedTime)
<meta property="article:published_time" content="{{ $publishedTime }}">
@endif
@if($modifiedTime)
<meta property="article:modified_time" content="{{ $modifiedTime }}">
@endif

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $currentUrl }}">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ Str::limit($description, 200) }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<!-- Canonical URL -->
<link rel="canonical" href="{{ $currentUrl }}">

<!-- Alternate Languages (if needed) -->
<link rel="alternate" hreflang="fr" href="{{ $currentUrl }}">

<!-- Structured Data (JSON-LD) -->
@if($structuredData)
<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif

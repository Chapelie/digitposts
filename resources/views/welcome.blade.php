@extends('layouts.app')

@section('title', 'DigitPosts - Formations & Événements Professionnels')

@section('content')
    {{-- Barre "Mise en ligne" après publication --}}
    @if(session('offer_published'))
    <div id="offer-published-bar" class="relative z-40 flex items-center justify-between gap-4 px-4 py-3 bg-green-600 text-white shadow-md animate-fade-in-down">
        <div class="flex items-center gap-3 min-w-0">
            <span class="flex-shrink-0 w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </span>
            <p class="text-sm font-medium truncate">Votre offre a été mise en ligne avec succès.</p>
        </div>
        <button type="button" onclick="document.getElementById('offer-published-bar').remove()" class="flex-shrink-0 p-1.5 rounded-md hover:bg-white/20 transition-colors" aria-label="Fermer">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <script>
        (function() {
            var bar = document.getElementById('offer-published-bar');
            if (bar) setTimeout(function() { bar.remove(); }, 6000);
        })();
    </script>
    @endif

    <!-- Hero Section with Swiper (image + écritures par slide) -->
    @php $swiperFeeds = $swiperFeeds ?? collect(); @endphp
    <section class="relative bg-white text-gray-900 overflow-hidden min-h-[420px] md:min-h-[500px] flex flex-col">
        <!-- Hero Swiper : slide intro + slides formations/événements (toujours affiché, au moins l'intro) -->
        <div class="relative w-full h-[320px] md:h-[380px]">
            <div class="swiper hero-swiper h-full w-full">
                <div class="swiper-wrapper">
                    {{-- Slide 1 : intro --}}
                    <div class="swiper-slide">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50"></div>
                        <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 20% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(147, 51, 234, 0.1) 0%, transparent 50%);"></div>
                        <div class="absolute inset-0 z-10 flex flex-col items-center justify-center text-center px-4 py-8">
                            <div class="flex flex-wrap items-center justify-center gap-3 mb-3">
                                <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                                    Plateforme de Formations & Événements
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-400/90 text-amber-900 rounded-full text-xs font-bold shadow-lg animate-crown-glow">
                                    <svg class="w-4 h-4 animate-crown-bounce" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v2h14v-2z"/></svg>
                                    Gratuit
                                </span>
                            </div>
                            <h1 class="text-2xl md:text-4xl lg:text-5xl font-bold mb-2 leading-tight text-gray-900">
                                <span>Découvrez les</span><br><span class="text-blue-600">Meilleures Formations</span><br><span>& Événements</span>
                            </h1>
                            <p class="text-sm md:text-base text-gray-600 max-w-2xl mx-auto">
                                {{ config('digitposts.description_short') }} Professionnels, étudiants et organisations : valorisez ou accédez à des opportunités de développement.
                            </p>
                        </div>
                    </div>
                    @foreach($swiperFeeds as $feed)
                        @php 
                            $item = $feed->feedable; 
                            $isEvent = $feed->feedable_type === \App\Models\Event::class;
                            $imgSrc = ($item && !empty($item->file)) ? asset('storage/' . $item->file) : asset('asset/image1_large.jpg');
                        @endphp
                        <div class="swiper-slide">
                            <div class="absolute inset-0">
                                <img src="{{ $imgSrc }}" alt="{{ $item?->title ?? '' }} - {{ $isEvent ? 'Événement' : 'Formation' }} sur DigitPosts" class="w-full h-full object-cover" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black/70"></div>
                            </div>
                            <div class="absolute inset-0 z-10 flex flex-col items-center justify-center text-center px-4 py-8">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $isEvent ? 'bg-amber-400/90 text-amber-900' : 'bg-blue-500/90 text-white' }} rounded-full text-xs font-bold shadow-lg mb-3">
                                    <svg class="w-4 h-4 animate-crown-bounce" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v2h14v-2z"/></svg>
                                    {{ $isEvent ? 'Événement' : 'Formation' }}
                                </span>
                                <h2 class="text-xl md:text-3xl lg:text-4xl font-bold text-white drop-shadow-lg mb-2 max-w-4xl">{{ $item->title }}</h2>
                                @if($item->start_date)
                                    <p class="text-sm md:text-base text-blue-200 drop-shadow-md">
                                        {{ \Carbon\Carbon::parse($item->start_date)->translatedFormat('l d F Y' . ($isEvent ? ' \à H:i' : '')) }}
                                    </p>
                                @endif
                                @if(!empty($item->location ?? $item->place ?? null))
                                    <p class="text-sm text-gray-200 mt-1 drop-shadow">{{ $item->location ?? $item->place }}</p>
                                @endif
                                <a href="{{ route('campaigns.show', $feed->id) }}" class="mt-4 inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-300 shadow-lg">
                                    {{ $isEvent ? "Voir l'événement" : 'Voir la formation' }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="hero-swiper-pagination absolute bottom-3 left-0 right-0 z-20"></div>
            </div>
        </div>

        <div class="relative z-10 container mx-auto px-4 py-6 md:py-8 w-full bg-white/95 backdrop-blur-sm border-t border-white/20">
            <div class="max-w-4xl mx-auto text-center">
                @if($swiperFeeds->count() === 0)
                <!-- Badge + titre (affichés aussi sous le swiper quand aucune slide feed) -->
                <div class="flex flex-wrap items-center justify-center gap-3 mb-4">
                    <div class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                        Plateforme de Formations & Événements
                    </div>
                    <div class="crown-badge inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-400/90 text-amber-900 rounded-full text-xs font-bold shadow-lg animate-crown-glow">
                        <svg class="w-4 h-4 animate-crown-bounce" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v2h14v-2z"/></svg>
                        Gratuit
                    </div>
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 leading-tight text-gray-900">
                    <span>Découvrez les</span><br><span class="text-blue-600">Meilleures Formations</span><br><span>& Événements</span>
                </h1>
                <p class="text-base md:text-lg text-gray-600 max-w-3xl mx-auto mb-6">
                    {{ config('digitposts.description_short') }} Professionnels, étudiants et organisations : valorisez ou accédez à des opportunités de développement.
                </p>
                @endif

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center mb-8">
                    <a href="#activities" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-base transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-blue-500/25">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12l2 2 4-4"></path>
                            <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"></path>
                        </svg>
                        Voir les Activités
                    </a>
                    @auth
                        <a href="{{ route('campaigns.create') }}" class="inline-flex items-center justify-center px-7 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-base transition-all duration-300 transform hover:scale-105 shadow-lg ring-2 ring-blue-800/60 ring-offset-2 ring-offset-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Créer une Activité
                        </a>
                    @else
                        <a href="{{ route('campaigns.create') }}" class="inline-flex items-center justify-center px-7 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-base transition-all duration-300 transform hover:scale-105 shadow-lg ring-2 ring-blue-800/60 ring-offset-2 ring-offset-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Créer une Activité
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border-2 font-semibold rounded-lg text-base transition-all duration-300 {{ $swiperFeeds->count() > 0 ? 'border-gray-300 text-gray-700 hover:bg-gray-100' : 'border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                            S'inscrire
                        </a>
                    @endauth
                </div>

                <!-- Accès rapides : 3 tuiles côte à côte (même style que l’ancien bandeau chiffres du welcome) -->
                <p class="text-sm mb-3 text-gray-700">Explorer la plateforme</p>
                <div class="hero-quick-grid mx-auto grid w-full max-w-4xl grid-cols-3 gap-2 sm:gap-3 md:gap-4 items-stretch">
                    <a href="#section-trainings" class="hero-stat-tile group flex min-h-[5.5rem] sm:min-h-[6.25rem] flex-col items-center justify-center gap-1.5 text-center rounded-lg border border-white/20 bg-white/90 p-2.5 sm:p-3 shadow-lg backdrop-blur-sm transition-all duration-300 hover:border-blue-200/80 hover:shadow-xl md:gap-2 md:p-4">
                        <i data-lucide="graduation-cap" class="hero-lucide h-8 w-8 shrink-0 text-blue-600 transition-transform duration-300 group-hover:scale-110 sm:h-10 sm:w-10 md:h-11 md:w-11" aria-hidden="true"></i>
                        <span class="text-[11px] font-semibold leading-tight text-gray-800 sm:text-xs md:text-sm">Formations</span>
                    </a>
                    <a href="#section-events" class="hero-stat-tile group flex min-h-[5.5rem] sm:min-h-[6.25rem] flex-col items-center justify-center gap-1.5 text-center rounded-lg border border-white/20 bg-white/90 p-2.5 sm:p-3 shadow-lg backdrop-blur-sm transition-all duration-300 hover:border-purple-200/80 hover:shadow-xl md:gap-2 md:p-4">
                        <i data-lucide="calendar-days" class="hero-lucide h-8 w-8 shrink-0 text-purple-600 transition-transform duration-300 group-hover:scale-110 sm:h-10 sm:w-10 md:h-11 md:w-11" aria-hidden="true"></i>
                        <span class="text-[11px] font-semibold leading-tight text-gray-800 sm:text-xs md:text-sm">Événements</span>
                    </a>
                    @auth
                        @php
                            $hasActiveSubscription = \App\Models\Subscription::hasActiveSubscription(Auth::id(), \App\Models\SubscriptionPlan::TYPE_FREE_EVENTS);
                        @endphp
                        @if($hasActiveSubscription)
                            <a href="{{ route('home', ['free' => 'true']) }}#activities" class="volet-gratuit volet-gratuit--animated relative flex min-h-[5.5rem] sm:min-h-[6.25rem] flex-col items-center justify-center gap-1.5 overflow-hidden rounded-lg border-2 border-amber-400 bg-white/95 p-2.5 text-center shadow-lg backdrop-blur-sm transition-all duration-300 hover:bg-white hover:shadow-xl sm:p-3 md:gap-2 md:p-4">
                                <span class="relative z-10 inline-flex shrink-0 rounded-xl bg-gradient-to-br from-amber-100 to-amber-200/80 p-2 text-amber-800 shadow-inner gratuit-lucide-wrap sm:p-2.5">
                                    <i data-lucide="gift" class="gratuit-lucide h-7 w-7 sm:h-8 sm:w-8 md:h-9 md:w-9" aria-hidden="true"></i>
                                </span>
                                <span class="relative z-10 text-[11px] font-bold leading-tight text-amber-900 sm:text-xs md:text-sm">Gratuits</span>
                                <span class="relative z-10 line-clamp-2 text-[9px] font-medium leading-snug text-amber-800/85 sm:text-xs">Sans frais</span>
                            </a>
                        @else
                            <a href="{{ route('subscriptions.checkout', ['plan' => 'free_events']) }}" class="volet-gratuit volet-gratuit--animated relative flex min-h-[5.5rem] sm:min-h-[6.25rem] flex-col items-center justify-center gap-1.5 overflow-hidden rounded-lg border-2 border-amber-400 bg-white/95 p-2.5 text-center shadow-lg backdrop-blur-sm transition-all duration-300 hover:bg-white hover:shadow-xl sm:p-3 md:gap-2 md:p-4">
                                <span class="relative z-10 inline-flex shrink-0 rounded-xl bg-gradient-to-br from-amber-100 to-amber-200/80 p-2 text-amber-800 shadow-inner gratuit-lucide-wrap sm:p-2.5">
                                    <i data-lucide="gift" class="gratuit-lucide h-7 w-7 sm:h-8 sm:w-8 md:h-9 md:w-9" aria-hidden="true"></i>
                                </span>
                                <span class="relative z-10 text-[11px] font-bold leading-tight text-amber-900 sm:text-xs md:text-sm">Gratuits</span>
                                <span class="relative z-10 line-clamp-2 text-[9px] font-medium leading-snug text-amber-800/85 sm:text-xs">Accès abonnement</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="volet-gratuit volet-gratuit--animated relative flex min-h-[5.5rem] sm:min-h-[6.25rem] flex-col items-center justify-center gap-1.5 overflow-hidden rounded-lg border-2 border-amber-400 bg-white/95 p-2.5 text-center shadow-lg backdrop-blur-sm transition-all duration-300 hover:bg-white hover:shadow-xl sm:p-3 md:gap-2 md:p-4">
                            <span class="relative z-10 inline-flex shrink-0 rounded-xl bg-gradient-to-br from-amber-100 to-amber-200/80 p-2 text-amber-800 shadow-inner gratuit-lucide-wrap sm:p-2.5">
                                <i data-lucide="gift" class="gratuit-lucide h-7 w-7 sm:h-8 sm:w-8 md:h-9 md:w-9" aria-hidden="true"></i>
                            </span>
                            <span class="relative z-10 text-[11px] font-bold leading-tight text-amber-900 sm:text-xs md:text-sm">Gratuits</span>
                            <span class="relative z-10 line-clamp-2 text-[9px] font-medium leading-snug text-amber-800/85 sm:text-xs">Connexion</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section id="filter" class="py-6 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
                <div class="text-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Filtrer les activités</h2>
                    <p class="text-sm text-gray-600">Catégorie et zone géographique</p>
                </div>

                <!-- Filtre Zone -->
                <div class="flex flex-wrap items-center justify-center gap-3 mb-4 pb-4 border-b border-gray-100">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Zone</span>
                    @php $zones = $zones ?? []; @endphp
                    <select onchange="window.location.href = this.value" class="rounded-lg border border-gray-300 text-sm py-1.5 px-3 bg-white">
                        <option value="{{ route('home', array_merge(request()->only(['category', 'free']), ['zone' => 'all'])) }}#activities">Toutes les villes</option>
                        @foreach($zones as $zone)
                            <option value="{{ route('home', array_merge(request()->only(['category', 'free']), ['zone' => $zone['id']])) }}#activities" {{ request('zone') === $zone['id'] ? 'selected' : '' }}>{{ $zone['name'] }} – {{ $zone['region'] }}</option>
                        @endforeach
                    </select>
                </div>

                @php
                    $_hcf = config('digitposts.home_category_filter', []);
                    $_cfSpace = (int) ($_hcf['space_between'] ?? 8);
                    $_cfNav = (bool) ($_hcf['show_navigation'] ?? true);
                @endphp
                <!-- Puces catégories : Swiper horizontal (swipe / flèches) -->
                <div class="category-filter-chips-wrap relative mx-auto w-full max-w-6xl">
                    @if($_cfNav)
                        <div class="category-filter-chips-prev swiper-button-prev" aria-label="Catégories précédentes"></div>
                        <div class="category-filter-chips-next swiper-button-next" aria-label="Catégories suivantes"></div>
                    @endif
                    <div
                        id="category-filter-chips"
                        class="swiper category-filter-chips"
                        data-space-between="{{ $_cfSpace }}"
                        data-nav="{{ $_cfNav ? '1' : '0' }}"
                    >
                        <div class="swiper-wrapper items-center py-1">
                            <div class="swiper-slide !h-auto !w-auto shrink-0">
                                <a href="{{ route('home', request()->only(['zone'])) }}#activities"
                                   class="inline-flex whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium shadow transition-all duration-300 items-center gap-2 {{ !$selectedCategory && !$showFreeOnly ? 'bg-blue-600 text-white shadow-lg ring-2 ring-blue-200' : 'bg-white text-gray-700 hover:bg-blue-50 hover:shadow-md' }}">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
                                    Toutes les activités
                                </a>
                            </div>
                            @foreach($categories as $category)
                                <div class="swiper-slide !h-auto !w-auto shrink-0">
                                    <a href="{{ route('home', array_merge(request()->only(['zone']), ['category' => $category->id])) }}#activities"
                                       class="inline-flex whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium shadow transition-all duration-300 items-center gap-2 {{ $selectedCategory == $category->id ? 'bg-blue-600 text-white shadow-lg ring-2 ring-blue-200' : 'bg-white text-gray-700 hover:bg-blue-50 hover:shadow-md' }}">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20z"/></svg>
                                        {{ $category->name }}
                                    </a>
                                </div>
                            @endforeach
                            @auth
                                @php
                                    $hasActiveSubscription = \App\Models\Subscription::hasActiveSubscription(Auth::id(), \App\Models\SubscriptionPlan::TYPE_FREE_EVENTS);
                                @endphp
                                @if($hasActiveSubscription)
                                    <div class="swiper-slide !h-auto !w-auto shrink-0">
                                        <a href="{{ route('home', array_merge(request()->only(['zone']), ['free' => 'true'])) }}#activities"
                                           class="inline-flex whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium shadow transition-all duration-300 items-center gap-2 {{ $showFreeOnly ? 'bg-green-600 text-white shadow-lg ring-2 ring-green-200' : 'bg-white text-gray-700 hover:bg-green-50 hover:shadow-md' }}">
                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3"/></svg>
                                            Gratuites uniquement
                                        </a>
                                    </div>
                                @else
                                    <div class="swiper-slide !h-auto !w-auto shrink-0">
                                        <a href="{{ route('subscriptions.checkout', ['plan' => 'free_events']) }}"
                                           class="inline-flex whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium shadow transition-all duration-300 items-center gap-2 bg-white text-gray-700 hover:bg-green-50 hover:shadow-md">
                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3"/></svg>
                                            Gratuites uniquement
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="swiper-slide !h-auto !w-auto shrink-0">
                                    <a href="{{ route('login') }}"
                                       class="inline-flex whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium shadow transition-all duration-300 items-center gap-2 bg-white text-gray-700 hover:bg-green-50 hover:shadow-md">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3"/></svg>
                                        Gratuites uniquement
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                    <p class="mt-2 text-center text-xs text-gray-500">Glissez horizontalement pour voir toutes les catégories @if($_cfNav)<span class="hidden md:inline">; les flèches aident sur grand écran.</span>@endif</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Activities Section -->
    <section id="activities" class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <!-- Trainings Section -->
            <div id="section-trainings" class="mb-16 scroll-mt-24">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Formations Disponibles</h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-blue-600 to-blue-800 mx-auto"></div>
                </div>

                @if($trainingFeeds->isEmpty())
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm text-center py-12 px-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Aucune formation disponible</h3>
                        <p class="text-gray-500 max-w-md mx-auto">Nous n'avons aucune formation programmée pour le moment. Revenez plus tard !</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($trainingFeeds as $feed)
                            @php $training = $feed->feedable; @endphp
                            @if($training)
                            <div class="group h-full">
                                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-md hover:border-gray-300 transition-all duration-300 h-full flex flex-col">
                                    @if($training->file)
                                        <div class="h-48 overflow-hidden flex-shrink-0">
                                            <img src="{{ asset('storage/' . $training->file) }}" 
                                                 alt="{{ $training->title }} - Formation professionnelle au Burkina Faso" 
                                                 loading="lazy"
                                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 cursor-pointer image-zoom-trigger"
                                                 data-image-url="{{ asset('storage/' . $training->file) }}"
                                                 data-title="{{ $training->title }}">
                                        </div>
                                    @else
                                        <div class="h-48 bg-gradient-to-r from-blue-100 to-blue-200 flex items-center justify-center flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="p-6 flex-1 flex flex-col min-h-0">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                Formation
                                            </span>
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $training->is_free ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $training->formatted_price }}
                                            </span>
                                        </div>

                                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                                            <a href="{{ route('campaigns.show', $feed->id) }}" class="hover:text-blue-600 transition-colors">
                                                {{ $training->title }}
                                            </a>
                                        </h3>
                                        <p class="text-gray-600 mb-4 line-clamp-3 flex-1">{{ Str::limit($training->description, 120) }}</p>

                                        <div class="space-y-2 mb-4 min-w-0">
                                            <div class="flex items-center text-sm text-gray-500 min-w-0 overflow-hidden">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="truncate block min-w-0" title="{{ $training->end_date ? \Carbon\Carbon::parse($training->start_date)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($training->end_date)->format('d/m/Y') : \Carbon\Carbon::parse($training->start_date)->format('d/m/Y') }}">
                                                    @if($training->end_date)
                                                        {{ \Carbon\Carbon::parse($training->start_date)->format('d/m/y') }} → {{ \Carbon\Carbon::parse($training->end_date)->format('d/m/y') }}
                                                    @else
                                                        <span class="text-orange-600 font-medium">Coming Soon</span>
                                                        <span class="text-gray-400"> ({{ \Carbon\Carbon::parse($training->start_date)->format('d/m/y') }})</span>
                                                    @endif
                                                </span>
                                            </div>

                                            @if($training->location)
                                                <div class="flex items-center text-sm text-gray-500 min-w-0 overflow-hidden">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span class="truncate block min-w-0">{{ $training->location }} @if($training->place)({{ $training->place }})@endif</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-auto pt-4 border-t">
                                            <a href="{{ route('inscriptions.create', $feed->id) }}" class="block w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition-colors transform hover:scale-105 text-center">
                                                Participer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Events Section -->
            <div id="section-events" class="scroll-mt-24">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Événements</h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-purple-600 to-purple-800 mx-auto"></div>
                </div>

                @if($eventFeeds->isEmpty())
                    <div class="rounded-xl border border-gray-200 bg-white shadow-sm text-center py-12 px-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Aucun événement disponible</h3>
                        <p class="text-gray-500 max-w-md mx-auto">Nous n'avons aucun événement programmé pour le moment. Revenez plus tard !</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($eventFeeds as $feed)
                            @php $event = $feed->feedable; @endphp
                            @if($event)
                            <div class="group h-full">
                                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-md hover:border-gray-300 transition-all duration-300 h-full flex flex-col">
                                    @if($event->file)
                                        <div class="h-48 overflow-hidden flex-shrink-0">
                                            <img src="{{ asset('storage/' . $event->file) }}" 
                                                 alt="{{ $event->title }} - Événement professionnel au Burkina Faso" 
                                                 loading="lazy"
                                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110 cursor-pointer image-zoom-trigger"
                                                 data-image-url="{{ asset('storage/' . $event->file) }}"
                                                 data-title="{{ $event->title }}">
                                        </div>
                                    @else
                                        <div class="h-48 bg-gradient-to-r from-purple-100 to-purple-200 flex items-center justify-center flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif

                                    <div class="p-6 flex-1 flex flex-col min-h-0">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                                Événement
                                            </span>
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $event->is_free ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $event->formatted_price }}
                                            </span>
                                        </div>

                                        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">
                                            <a href="{{ route('campaigns.show', $feed->id) }}" class="hover:text-purple-600 transition-colors">
                                                {{ $event->title }}
                                            </a>
                                        </h3>
                                        <p class="text-gray-600 mb-4 line-clamp-3 flex-1">{{ Str::limit($event->description, 120) }}</p>

                                        <div class="space-y-2 mb-4 min-w-0">
                                            <div class="flex items-center text-sm text-gray-500 min-w-0 overflow-hidden">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="truncate block min-w-0" title="{{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y H:i') }}">{{ \Carbon\Carbon::parse($event->start_date)->format('d/m/y H:i') }}</span>
                                            </div>

                                            @if(!empty($event->location))
                                                <div class="flex items-center text-sm text-gray-500 min-w-0 overflow-hidden">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span class="truncate block min-w-0">{{ $event->location }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-auto pt-4 border-t">
                                            <a href="{{ route('inscriptions.create', $feed->id) }}" class="block w-full py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md text-sm font-medium transition-colors transform hover:scale-105 text-center">
                                                Participer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-blue-700 to-blue-900 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6 animate-fade-in-up">Prêt à Organiser Votre Propre Événement ?</h2>
            <p class="text-lg md:text-xl text-blue-100 max-w-3xl mx-auto mb-8 animate-fade-in-up delay-200">
                Créez votre propre programme de formation ou événement et connectez-vous avec des participants du monde entier.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up delay-300">
                @auth
                    <a href="{{ route('campaigns.create') }}" class="inline-flex items-center px-8 py-3 bg-white text-blue-600 rounded-lg text-lg font-semibold hover:bg-blue-50 transition duration-300 transform hover:scale-105">
                        Créer une Activité
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 bg-white text-blue-600 rounded-lg text-lg font-semibold hover:bg-blue-50 transition duration-300 transform hover:scale-105">
                        Commencer
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3 border-2 border-white text-white rounded-lg text-lg font-semibold hover:bg-white hover:bg-opacity-10 transition duration-300 transform hover:scale-105">
                        Se Connecter
                    </a>
                @endauth
            </div>
            @include('partials.social-share', ['url' => url()->current(), 'title' => 'DigitPosts - Formations & Événements'])
        </div>
    </section>

    <!-- Image Zoom Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
        <div class="relative h-full flex items-center justify-center p-4">
            <div class="relative max-w-4xl max-h-full">
                <!-- Close Button -->
                <button id="closeModal" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
                
                <!-- Image Container -->
                <div class="relative bg-white rounded-lg shadow-2xl overflow-hidden">
                    <img id="modalImage" src="" alt="" class="max-w-full max-h-[80vh] object-contain">
                    <div id="modalTitle" class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4 text-white">
                        <h3 class="text-lg font-semibold"></h3>
                    </div>
                </div>
                
                <!-- Zoom Controls -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
                    <button id="zoomIn" class="bg-white/20 backdrop-blur-sm rounded-full p-2 text-white hover:bg-white/30 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                    <button id="zoomOut" class="bg-white/20 backdrop-blur-sm rounded-full p-2 text-white hover:bg-white/30 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </button>
                    <button id="resetZoom" class="bg-white/20 backdrop-blur-sm rounded-full p-2 text-white hover:bg-white/30 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                            <path d="M21 3v5h-5"></path>
                            <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                            <path d="M3 21v-5h5"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Simple Hero Animations */
        .animate-fade-in-down {
            animation: fadeInDown 0.8s ease-out;
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        .animate-pulse-subtle {
            animation: pulseSubtle 2.5s ease-in-out infinite;
        }
        @keyframes pulseSubtle {
            0%, 100% { opacity: 1; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
            50% { opacity: 0.95; box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.35); }
        }

        /* Tuile « gratuits » : mise en avant animée (sans chiffres) */
        .volet-gratuit--animated {
            animation: gratuitCardGlow 2.8s ease-in-out infinite;
        }
        @keyframes gratuitCardGlow {
            0%, 100% {
                border-color: rgb(251 191 36 / 0.85);
                box-shadow: 0 4px 20px rgb(245 158 11 / 0.22);
            }
            50% {
                border-color: rgb(234 179 8 / 1);
                box-shadow: 0 10px 36px rgb(245 158 11 / 0.4);
            }
        }
        .volet-gratuit--animated::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(
                105deg,
                transparent 36%,
                rgb(255 255 255 / 0.55) 50%,
                transparent 64%
            );
            background-size: 220% 100%;
            animation: gratuitShimmer 3.8s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes gratuitShimmer {
            0% { background-position: 120% 0; }
            100% { background-position: -120% 0; }
        }
        .gratuit-lucide-wrap .gratuit-lucide,
        .volet-gratuit--animated .gratuit-lucide {
            animation: gratuitIconFloat 2.2s ease-in-out infinite;
        }
        @keyframes gratuitIconFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            35% { transform: translateY(-6px) rotate(-5deg); }
            70% { transform: translateY(-3px) rotate(5deg); }
        }
        .hero-lucide,
        .hero-stat-tile [data-lucide] {
            stroke-width: 1.75;
        }

        .animate-crown-glow {
            animation: crownGlow 2s ease-in-out infinite;
        }
        @keyframes crownGlow {
            0%, 100% { box-shadow: 0 0 12px rgba(245, 158, 11, 0.4); transform: scale(1); }
            50% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.7); transform: scale(1.02); }
        }

        .animate-crown-bounce {
            animation: crownBounce 1.5s ease-in-out infinite;
        }
        @keyframes crownBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Card Alignment */
        .grid {
            align-items: stretch;
        }
        
        .group {
            display: flex;
            flex-direction: column;
        }
        
        .group > div {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .min-h-0 {
            min-height: 0;
        }
        
        .flex-shrink-0 {
            flex-shrink: 0;
        }
        
        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Image Zoom Styles */
        #modalImage {
            transition: transform 0.3s ease;
        }
        
        .image-zoom-trigger:hover {
            transform: scale(1.02);
        }

        /* Hero Swiper : hauteur fixe pour que Swiper calcule correctement */
        .hero-swiper {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .hero-swiper .swiper-wrapper {
            height: 100%;
        }

        .hero-swiper .swiper-slide {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .hero-swiper .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Pagination hero : points visibles */
        .hero-swiper-pagination.swiper-pagination {
            position: absolute;
            bottom: 0.75rem;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 20;
        }

        .hero-swiper-pagination .swiper-pagination-bullet {
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.6);
            opacity: 1;
            transition: background 0.2s, transform 0.2s;
        }

        .hero-swiper-pagination .swiper-pagination-bullet-active {
            background: #3b82f6;
            transform: scale(1.25);
        }

        /* Swiper : bandeau des catégories (puces, scroll horizontal) */
        .category-filter-chips {
            overflow: visible;
        }
        .category-filter-chips .swiper-slide {
            width: auto;
            height: auto;
        }
        .category-filter-chips-wrap .swiper-button-prev,
        .category-filter-chips-wrap .swiper-button-next {
            width: 2.25rem;
            height: 2.25rem;
            background: #fff;
            border-radius: 9999px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.12);
            color: #374151;
        }
        .category-filter-chips-wrap .swiper-button-prev:after,
        .category-filter-chips-wrap .swiper-button-next:after {
            font-size: 0.7rem;
            font-weight: 700;
        }
        .category-filter-chips-wrap .swiper-button-disabled {
            opacity: 0.25;
            pointer-events: none;
        }
        @media (max-width: 767px) {
            .category-filter-chips-wrap .swiper-button-prev,
            .category-filter-chips-wrap .swiper-button-next {
                display: none;
            }
        }
        @media (min-width: 768px) {
            .category-filter-chips {
                padding-left: 2.75rem;
                padding-right: 2.75rem;
            }
        }

        /* Swiper Styles */
        .events-swiper {
            padding-bottom: 50px;
        }

        .events-swiper .swiper-slide {
            height: auto;
        }

        .events-swiper .swiper-button-next,
        .events-swiper .swiper-button-prev {
            background: #3b82f6;
            color: white;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .events-swiper .swiper-button-next:hover,
        .events-swiper .swiper-button-prev:hover {
            background: #2563eb;
            transform: scale(1.1);
        }

        .events-swiper .swiper-button-next:after,
        .events-swiper .swiper-button-prev:after {
            font-size: 18px;
            font-weight: bold;
        }

        .events-swiper .swiper-pagination-bullet {
            background: #3b82f6;
            opacity: 0.5;
        }

        .events-swiper .swiper-pagination-bullet-active {
            background: #3b82f6;
            opacity: 1;
        }

        @media (max-width: 640px) {
            .events-swiper .swiper-button-next,
            .events-swiper .swiper-button-prev {
                display: none;
            }
        }
    </style>

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <script>
        // Variables pour le zoom
        let currentZoom = 1;
        const zoomStep = 0.2;
        const maxZoom = 3;
        const minZoom = 0.5;

        // Éléments du modal
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const closeModal = document.getElementById('closeModal');
        const zoomIn = document.getElementById('zoomIn');
        const zoomOut = document.getElementById('zoomOut');
        const resetZoom = document.getElementById('resetZoom');

        // Ouvrir le modal
        document.querySelectorAll('.image-zoom-trigger').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Empêcher la navigation vers la page de détail
                const imageUrl = this.getAttribute('data-image-url');
                const title = this.getAttribute('data-title');
                
                modalImage.src = imageUrl;
                modalImage.alt = title;
                modalTitle.querySelector('h3').textContent = title;
                
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Reset zoom
                currentZoom = 1;
                modalImage.style.transform = `scale(${currentZoom})`;
            });
        });

        // Fermer le modal
        function closeImageModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            currentZoom = 1;
            modalImage.style.transform = `scale(${currentZoom})`;
        }

        closeModal.addEventListener('click', closeImageModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeImageModal();
            }
        });

        // Zoom in
        zoomIn.addEventListener('click', function() {
            if (currentZoom < maxZoom) {
                currentZoom += zoomStep;
                modalImage.style.transform = `scale(${currentZoom})`;
            }
        });

        // Zoom out
        zoomOut.addEventListener('click', function() {
            if (currentZoom > minZoom) {
                currentZoom -= zoomStep;
                modalImage.style.transform = `scale(${currentZoom})`;
            }
        });

        // Reset zoom
        resetZoom.addEventListener('click', function() {
            currentZoom = 1;
            modalImage.style.transform = `scale(${currentZoom})`;
        });

        // Zoom avec la molette de la souris
        modalImage.addEventListener('wheel', function(e) {
            e.preventDefault();
            if (e.deltaY < 0) {
                // Zoom in
                if (currentZoom < maxZoom) {
                    currentZoom += zoomStep;
                    modalImage.style.transform = `scale(${currentZoom})`;
                }
            } else {
                // Zoom out
                if (currentZoom > minZoom) {
                    currentZoom -= zoomStep;
                    modalImage.style.transform = `scale(${currentZoom})`;
                }
            }
        });

        // Fermer le modal avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeImageModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Scroll automatique sur la section activités si filtre
            const url = new URL(window.location.href);
            if(url.hash === '#activities' || url.search.includes('category') || url.search.includes('free')) {
                setTimeout(() => {
                    const section = document.getElementById('activities');
                    if(section) section.scrollIntoView({behavior: 'smooth'});
                }, 200);
            }
        });
    </script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script>

    <script>
        // Hero Swiper : toujours initialiser (au moins la slide intro)
        (function() {
            function initHeroSwiper() {
                var el = document.querySelector('.hero-swiper');
                if (!el) return;
                if (typeof Swiper === 'undefined') {
                    setTimeout(initHeroSwiper, 50);
                    return;
                }
                var totalSlides = 1 + {{ $swiperFeeds->count() }};
                new Swiper('.hero-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 0,
                    loop: totalSlides > 1,
                    autoplay: { delay: 5000, disableOnInteraction: false },
                    effect: 'fade',
                    fadeEffect: { crossFade: true },
                    speed: 600,
                    pagination: { el: '.hero-swiper-pagination', clickable: true },
                    allowTouchMove: true,
                    observer: true,
                    observeParents: true,
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initHeroSwiper);
            } else {
                initHeroSwiper();
            }
        })();

        (function () {
            function initCategoryFilterChipsSwiper() {
                var el = document.getElementById('category-filter-chips');
                if (!el || el.dataset.inited) {
                    return;
                }
                if (typeof Swiper === 'undefined') {
                    setTimeout(initCategoryFilterChipsSwiper, 50);
                    return;
                }
                var wrap = el.closest('.category-filter-chips-wrap');
                var sp = parseInt(el.getAttribute('data-space-between') || '8', 10);
                var useNav = el.getAttribute('data-nav') === '1';
                var opts = {
                    slidesPerView: 'auto',
                    spaceBetween: isNaN(sp) ? 8 : sp,
                    freeMode: true,
                    grabCursor: true,
                    watchSlidesProgress: true,
                    resistanceRatio: 0.85,
                };
                if (useNav && wrap) {
                    var prev = wrap.querySelector('.category-filter-chips-prev');
                    var next = wrap.querySelector('.category-filter-chips-next');
                    if (prev && next) {
                        opts.navigation = { prevEl: prev, nextEl: next };
                    }
                }
                new Swiper(el, opts);
                el.dataset.inited = '1';
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initCategoryFilterChipsSwiper);
            } else {
                initCategoryFilterChipsSwiper();
            }
        })();

        (function () {
            function initLucideIcons() {
                if (typeof lucide === 'undefined') {
                    setTimeout(initLucideIcons, 40);
                    return;
                }
                lucide.createIcons();
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLucideIcons);
            } else {
                initLucideIcons();
            }
        })();
    </script>
@endsection

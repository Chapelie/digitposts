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
    <section class="relative bg-white text-gray-900 overflow-hidden min-h-[420px] md:min-h-[500px] flex flex-col">
        @if(isset($swiperFeeds) && $swiperFeeds->count() > 0)
        <!-- Hero Swiper : slide intro + slides formations/événements (image + écritures) -->
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
        @else
        <!-- Fallback sans swiper -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-purple-50"></div>
        <div class="absolute inset-0 opacity-30">
            <div class="absolute inset-0" style="background-image: radial-gradient(circle at 20% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(147, 51, 234, 0.1) 0%, transparent 50%);"></div>
        </div>
        @endif

        <div class="relative z-10 container mx-auto px-4 py-6 md:py-8 w-full {{ (isset($swiperFeeds) && $swiperFeeds->count() > 0) ? 'bg-white/95 backdrop-blur-sm border-t border-white/20' : '' }}">
            <div class="max-w-4xl mx-auto text-center">
                @if(!isset($swiperFeeds) || $swiperFeeds->count() === 0)
                <!-- Badge + Couronne (affichés seulement sans swiper) -->
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
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border-2 font-semibold rounded-lg text-base transition-all duration-300 {{ (isset($swiperFeeds) && $swiperFeeds->count() > 0) ? 'border-gray-300 text-gray-700 hover:bg-gray-100' : 'border-white/80 text-white hover:bg-white/10' }}">
                            S'inscrire
                        </a>
                    @endauth
                </div>

                <!-- Nombre de formations et événements disponibles -->
                <p class="text-sm mb-3 {{ (isset($swiperFeeds) && $swiperFeeds->count() > 0) ? 'text-gray-700' : 'text-white/90' }}">Formations et événements disponibles</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                    <div class="text-center p-3 bg-white/90 backdrop-blur-sm rounded-lg shadow-lg border border-white/20">
                        <div class="text-xl md:text-2xl font-bold text-blue-600 mb-0.5">{{ $trainingFeeds->count() }}</div>
                        <div class="text-xs text-gray-700 font-medium">Formations</div>
                    </div>
                    <div class="text-center p-3 bg-white/90 backdrop-blur-sm rounded-lg shadow-lg border border-white/20">
                        <div class="text-xl md:text-2xl font-bold text-purple-600 mb-0.5">{{ $eventFeeds->count() }}</div>
                        <div class="text-xs text-gray-700 font-medium">Événements</div>
                    </div>
                    <div class="text-center p-3 bg-white/90 backdrop-blur-sm rounded-lg shadow-lg border border-white/20">
                        <div class="text-xl md:text-2xl font-bold text-green-600 mb-0.5">{{ $upcomingCount }}</div>
                        <div class="text-xs text-gray-700 font-medium">À Venir</div>
                    </div>
                    @auth
                        @php
                            $hasActiveSubscription = \App\Models\Subscription::hasActiveSubscription(Auth::id(), \App\Models\SubscriptionPlan::TYPE_FREE_EVENTS);
                        @endphp
                        @if($hasActiveSubscription)
                            <a href="{{ route('home', ['free' => 'true']) }}#activities" class="volet-gratuit text-center p-3 bg-white/95 backdrop-blur-sm rounded-lg shadow-lg border-2 border-amber-400 hover:bg-white hover:shadow-xl transition-all duration-300 cursor-pointer group animate-pulse-subtle">
                                <span class="inline-flex items-center justify-center w-8 h-8 mx-auto mb-1 rounded-full bg-amber-100 text-amber-600 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-5 h-5 animate-crown-bounce" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v2h14v-2z"/></svg>
                                </span>
                                <div class="text-xl md:text-2xl font-bold text-orange-600 mb-0.5 group-hover:text-orange-700 transition-colors">{{ $freeCount }}</div>
                                <div class="text-xs text-gray-700 font-medium group-hover:text-orange-600 transition-colors">Gratuites</div>
                            </a>
                        @else
                            <a href="{{ route('subscriptions.checkout', ['plan' => 'free_events']) }}" class="volet-gratuit text-center p-3 bg-white/95 backdrop-blur-sm rounded-lg shadow-lg border-2 border-amber-400 hover:bg-white hover:shadow-xl transition-all duration-300 cursor-pointer group animate-pulse-subtle">
                                <span class="inline-flex items-center justify-center w-8 h-8 mx-auto mb-1 rounded-full bg-amber-100 text-amber-600 group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-5 h-5 animate-crown-bounce" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v2h14v-2z"/></svg>
                                </span>
                                <div class="text-xl md:text-2xl font-bold text-orange-600 mb-0.5 group-hover:text-orange-700 transition-colors">{{ $freeCount }}</div>
                                <div class="text-xs text-gray-700 font-medium group-hover:text-orange-600 transition-colors">Gratuites</div>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="volet-gratuit text-center p-3 bg-white/95 backdrop-blur-sm rounded-lg shadow-lg border-2 border-amber-400 hover:bg-white hover:shadow-xl transition-all duration-300 cursor-pointer group animate-pulse-subtle">
                            <span class="inline-flex items-center justify-center w-8 h-8 mx-auto mb-1 rounded-full bg-amber-100 text-amber-600 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 animate-crown-bounce" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3H5v2h14v-2z"/></svg>
                            </span>
                            <div class="text-xl md:text-2xl font-bold text-orange-600 mb-0.5 group-hover:text-orange-700 transition-colors">{{ $freeCount }}</div>
                            <div class="text-xs text-gray-700 font-medium group-hover:text-orange-600 transition-colors">Gratuites</div>
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

                <!-- Desktop Filter Catégories + Gratuit -->
                <div class="hidden md:block">
                    <div class="flex flex-wrap justify-center gap-2">
                    <a href="{{ route('home', request()->only(['zone'])) }}#activities" 
                       class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 flex items-center gap-2 shadow {{ !$selectedCategory && !$showFreeOnly ? 'bg-blue-600 text-white shadow-lg scale-105' : 'bg-white text-gray-700 hover:bg-blue-50 hover:shadow-md' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
                        Toutes les activités
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('home', array_merge(request()->only(['zone']), ['category' => $category->id])) }}#activities" 
                           class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 flex items-center gap-2 shadow {{ $selectedCategory == $category->id ? 'bg-blue-600 text-white shadow-lg scale-105' : 'bg-white text-gray-700 hover:bg-blue-50 hover:shadow-md' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20z"/></svg>
                            {{ $category->name }}
                        </a>
                    @endforeach
                    @auth
                        @php
                            $hasActiveSubscription = \App\Models\Subscription::hasActiveSubscription(Auth::id(), \App\Models\SubscriptionPlan::TYPE_FREE_EVENTS);
                        @endphp
                        @if($hasActiveSubscription)
                            <a href="{{ route('home', array_merge(request()->only(['zone']), ['free' => 'true'])) }}#activities" 
                               class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 flex items-center gap-2 shadow {{ $showFreeOnly ? 'bg-green-600 text-white shadow-lg scale-105' : 'bg-white text-gray-700 hover:bg-green-50 hover:shadow-md' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3"/></svg>
                                Gratuites uniquement
                            </a>
                        @else
                            <a href="{{ route('subscriptions.checkout', ['plan' => 'free_events']) }}" 
                               class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 flex items-center gap-2 shadow bg-white text-gray-700 hover:bg-green-50 hover:shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3"/></svg>
                                Gratuites uniquement
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-300 flex items-center gap-2 shadow bg-white text-gray-700 hover:bg-green-50 hover:shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3"/></svg>
                            Gratuites uniquement
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Mobile Filter -->
            <div class="md:hidden">
                <div class="relative">
                    <button id="mobile-filter-btn" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-left flex items-center justify-between shadow">
                        <span class="text-gray-700 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
                            @if($showFreeOnly)
                                Gratuites uniquement
                            @elseif($selectedCategory)
                                {{ $categories->firstWhere('id', $selectedCategory)->name }}
                            @else
                                Toutes les activités
                            @endif
                        </span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="mobile-filter-dropdown" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg z-50">
                        <div class="py-2">
                            <a href="{{ route('home') }}#activities" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 flex items-center gap-2 {{ !$selectedCategory && !$showFreeOnly ? 'bg-blue-50 text-blue-600' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/></svg>
                                Toutes les activités
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('home', ['category' => $category->id]) }}#activities" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 flex items-center gap-2 {{ $selectedCategory == $category->id ? 'bg-blue-50 text-blue-600' : '' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20z"/></svg>
                                    {{ $category->name }}
                                </a>
                            @endforeach
                            @auth
                                @php
                                    $hasActiveSubscription = \App\Models\Subscription::hasActiveSubscription(Auth::id(), \App\Models\SubscriptionPlan::TYPE_FREE_EVENTS);
                                @endphp
                                @if($hasActiveSubscription)
                                    <a href="{{ route('home', ['free' => 'true']) }}#activities" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 flex items-center gap-2 {{ $showFreeOnly ? 'bg-green-50 text-green-600' : '' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3"/></svg>
                                        Gratuites uniquement
                                    </a>
                                @else
                                    <a href="{{ route('subscriptions.checkout', ['plan' => 'free_events']) }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3"/></svg>
                                        Gratuites uniquement
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 8v4l3 3"/></svg>
                                    Gratuites uniquement
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </section>

    <!-- Activities Section -->
    <section id="activities" class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <!-- Trainings Section -->
            <div class="mb-16">
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
            <div>
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Événements à Venir</h2>
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
            // Mobile filter dropdown
            const filterBtn = document.getElementById('mobile-filter-btn');
            const filterDropdown = document.getElementById('mobile-filter-dropdown');
            const filterIcon = filterBtn.querySelector('svg');

            filterBtn.addEventListener('click', function() {
                filterDropdown.classList.toggle('hidden');
                filterIcon.classList.toggle('rotate-180');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!filterBtn.contains(event.target) && !filterDropdown.contains(event.target)) {
                    filterDropdown.classList.add('hidden');
                    filterIcon.classList.remove('rotate-180');
                }
            });

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
    
    <script>
        // Hero Swiper : initialisation après chargement du DOM et de la lib
        @if(isset($swiperFeeds) && $swiperFeeds->count() > 0)
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
        @endif
    </script>
@endsection

@extends('layouts.dashboard')

@section('content')
    <div class="space-y-8">
        <!-- Hero Section - Plus grand et plus attractif -->
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800 rounded-xl p-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex-1">
                    <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-4">Mes Campagnes</h1>
                    <p class="text-xl text-blue-100 mb-6">Gérez vos programmes de formation et événements avec facilité</p>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                            <span>{{ $campaigns->where('status', 'publiée')->count() }} Publiées</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                            <span>{{ $campaigns->where('status', 'brouillon')->count() }} Brouillons</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-blue-400 rounded-full"></div>
                            <span>{{ $campaigns->filter(function($feed) { return $feed->feedable_type === 'App\Models\Training'; })->count() }} Formations</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 bg-purple-400 rounded-full"></div>
                            <span>{{ $campaigns->filter(function($feed) { return $feed->feedable_type === 'App\Models\Event'; })->count() }} Événements</span>
                        </div>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <a href="{{ route('campaigns.create') }}" class="inline-flex items-center rounded-lg border-2 border-white bg-white/10 backdrop-blur-sm px-6 py-3 text-lg font-medium text-white shadow-lg hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Nouvelle Campagne
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters Section - Amélioré avec catégories dynamiques -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtres et recherche</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" placeholder="Rechercher..." class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pl-9">
                    </div>

                    <!-- Type Filter -->
                    <select class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="all">Tous les types</option>
                        <option value="training">Formation</option>
                        <option value="event">Événement</option>
                    </select>

                    <!-- Status Filter -->
                    <select class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="all">Tous les statuts</option>
                        <option value="publiée">Publié</option>
                        <option value="brouillon">Brouillon</option>
                        <option value="clôturée">Terminé</option>
                    </select>

                    <!-- Category Filter - Dynamique depuis la BD -->
                    <select class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="all">Toutes les catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button type="button" class="tab-button active border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Toutes les campagnes
                </button>
                <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Publiées
                </button>
                <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Brouillons
                </button>
                <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Terminées
                </button>
            </nav>
        </div>

        <!-- All Campaigns Tab -->
        <div id="all-campaigns" class="tab-content active space-y-8">
            @php
                $trainings = $campaigns->filter(function($feed) {
                    return $feed->feedable_type === 'App\Models\Training';
                });
                $events = $campaigns->filter(function($feed) {
                    return $feed->feedable_type === 'App\Models\Event';
                });
            @endphp

            <!-- Formations Section -->
            @if($trainings->count() > 0)
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-10 bg-gradient-to-b from-blue-500 to-blue-600 rounded-full"></div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Formations</h2>
                            <p class="text-gray-500">Vos programmes de formation</p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">{{ $trainings->count() }}</span>
                    </div>
                    
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($trainings as $feed)
                            @php
                                $campaign = $feed->feedable;
                                $isUpcoming = \Carbon\Carbon::parse($campaign->start_date)->isFuture();
                                $isPast = \Carbon\Carbon::parse($campaign->end_date)->isPast();
                            @endphp
                            
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-200 h-full flex flex-col overflow-hidden">
                                <!-- Card Header avec image de fond améliorée -->
                                <div class="relative h-32 bg-gradient-to-br from-blue-500 to-blue-600">
                                    @if($campaign->file)
                                        <div class="absolute inset-0 bg-cover bg-center cursor-pointer image-zoom-trigger" 
                                             style="background-image: url('{{ asset('storage/' . $campaign->file) }}');"
                                             data-image-url="{{ asset('storage/' . $campaign->file) }}"
                                             data-title="{{ $campaign->title }}">
                                            <div class="absolute inset-0 bg-black/40"></div>
                                            <!-- Icône de zoom -->
                                            <div class="absolute top-2 left-2 bg-white/20 backdrop-blur-sm rounded-full p-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="11" cy="11" r="8"></circle>
                                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                                    <line x1="11" y1="8" x2="11" y2="14"></line>
                                                    <line x1="8" y1="11" x2="14" y2="11"></line>
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                            </svg>
                                            <span class="text-sm font-medium">Formation</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Status Badge -->
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium 
                                            @if($feed->status === 'publiée') bg-green-500 text-white
                                            @elseif($feed->status === 'brouillon') bg-yellow-500 text-white
                                            @else bg-gray-500 text-white @endif">
                                            {{ ucfirst($feed->status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="p-6 flex-1 flex flex-col">
                                    <!-- Campaign Title -->
                                    <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $campaign->title }}</h3>

                                    <!-- Campaign Meta -->
                                    <div class="flex items-center text-sm text-gray-500 mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        @if($campaign->end_date)
                                            {{ \Carbon\Carbon::parse($campaign->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($campaign->end_date)->format('d M, Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($campaign->start_date)->format('d M, Y') }}
                                        @endif
                                    </div>

                                    <!-- Price -->
                                    <div class="mb-4">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium 
                                            @if($campaign->is_free) bg-gray-100 text-gray-800 @else bg-green-100 text-green-800 @endif">
                                            {{ $campaign->formatted_price }}
                                        </span>
                                    </div>

                                    <!-- Categories -->
                                    @if($campaign->categories->count() > 0)
                                        <div class="mb-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($campaign->categories->take(3) as $category)
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                                @if($campaign->categories->count() > 3)
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600">
                                                        +{{ $campaign->categories->count() - 3 }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Stats -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="text-sm text-gray-600">
                                            <span class="font-semibold text-gray-900">{{ $feed->registrations->count() }}</span> inscriptions
                                        </div>
                                        @if($campaign->location)
                                            <div class="text-xs text-gray-500 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                {{ $campaign->location }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="flex gap-2 mt-auto">
                                        <a href="{{ route('campaigns.show', $feed->id) }}" class="flex-1">
                                            <button type="button" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                Voir
                                            </button>
                                        </a>

                                        @if($feed->status === 'publiée')
                                            <a href="#" class="flex-1">
                                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full transition-colors">
                                                    Gérer
                                                </button>
                                            </a>
                                        @else
                                            <a href="#" class="flex-1">
                                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full transition-colors">
                                                    Modifier
                                                </button>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Événements Section -->
            @if($events->count() > 0)
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-2 h-10 bg-gradient-to-b from-purple-500 to-purple-600 rounded-full"></div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Événements</h2>
                            <p class="text-gray-500">Vos événements et rencontres</p>
                        </div>
                        <span class="bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded-full">{{ $events->count() }}</span>
                    </div>
                    
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach($events as $feed)
                            @php
                                $campaign = $feed->feedable;
                                $isUpcoming = \Carbon\Carbon::parse($campaign->start_date)->isFuture();
                                $isPast = \Carbon\Carbon::parse($campaign->start_date)->isPast();
                            @endphp
                            
                            <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-200 h-full flex flex-col overflow-hidden">
                                <!-- Card Header avec image de fond améliorée -->
                                <div class="relative h-32 bg-gradient-to-br from-purple-500 to-purple-600">
                                    @if($campaign->file)
                                        <div class="absolute inset-0 bg-cover bg-center cursor-pointer image-zoom-trigger" 
                                             style="background-image: url('{{ asset('storage/' . $campaign->file) }}');"
                                             data-image-url="{{ asset('storage/' . $campaign->file) }}"
                                             data-title="{{ $campaign->title }}">
                                            <div class="absolute inset-0 bg-black/40"></div>
                                            <!-- Icône de zoom -->
                                            <div class="absolute top-2 left-2 bg-white/20 backdrop-blur-sm rounded-full p-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="11" cy="11" r="8"></circle>
                                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                                    <line x1="11" y1="8" x2="11" y2="14"></line>
                                                    <line x1="8" y1="11" x2="14" y2="11"></line>
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M8 2v4"></path>
                                                <path d="M16 2v4"></path>
                                                <rect x="1" y="4" width="22" height="18" rx="2" ry="2"></rect>
                                                <path d="M1 10h22"></path>
                                            </svg>
                                            <span class="text-sm font-medium">Événement</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Status Badge -->
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium 
                                            @if($feed->status === 'publiée') bg-green-500 text-white
                                            @elseif($feed->status === 'brouillon') bg-yellow-500 text-white
                                            @else bg-gray-500 text-white @endif">
                                            {{ ucfirst($feed->status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="p-6 flex-1 flex flex-col">
                                    <!-- Campaign Title -->
                                    <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $campaign->title }}</h3>

                                    <!-- Campaign Meta -->
                                    <div class="flex items-center text-sm text-gray-500 mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($campaign->start_date)->format('d M, Y H:i') }}
                                    </div>

                                    <!-- Price -->
                                    <div class="mb-4">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium 
                                            @if($campaign->is_free) bg-gray-100 text-gray-800 @else bg-green-100 text-green-800 @endif">
                                            {{ $campaign->formatted_price }}
                                        </span>
                                    </div>

                                    <!-- Categories -->
                                    @if($campaign->categories->count() > 0)
                                        <div class="mb-4">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($campaign->categories->take(3) as $category)
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-purple-50 text-purple-700">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                                @if($campaign->categories->count() > 3)
                                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600">
                                                        +{{ $campaign->categories->count() - 3 }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Stats -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="text-sm text-gray-600">
                                            <span class="font-semibold text-gray-900">{{ $feed->registrations->count() }}</span> inscriptions
                                        </div>
                                        @if($campaign->location)
                                            <div class="text-xs text-gray-500 flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                    <circle cx="12" cy="10" r="3"></circle>
                                                </svg>
                                                {{ $campaign->location }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Card Footer -->
                                    <div class="flex gap-2 mt-auto">
                                        <a href="{{ route('campaigns.show', $feed->id) }}" class="flex-1">
                                            <button type="button" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 w-full transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                Voir
                                            </button>
                                        </a>

                                        @if($feed->status === 'publiée')
                                            <a href="{{ route('campaigns.registrations', $feed->id) }}" class="flex-1">
                                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-purple-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 w-full transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    Inscriptions
                                                </button>
                                            </a>
                                        @else
                                            <a href="#" class="flex-1">
                                                <button type="button" class="inline-flex items-center justify-center rounded-lg border border-transparent bg-purple-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 w-full transition-colors">
                                                    Modifier
                                                </button>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Empty State -->
            @if($campaigns->count() === 0)
                <div class="text-center py-16">
                    <div class="mx-auto h-24 w-24 text-gray-400 mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune campagne trouvée</h3>
                    <p class="text-gray-500 mb-6">Créez votre première campagne pour commencer votre aventure !</p>
                    <a href="{{ route('campaigns.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Créer une campagne
                    </a>
                </div>
            @endif
        </div>

        <!-- Published Campaigns Tab -->
        <div id="published-campaigns" class="tab-content hidden space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($campaigns->where('status', 'published') as $campaign)
                    <!-- Same card structure as above -->
                    @include('campaigns.partials.card', ['campaign' => $campaign])
                @endforeach
            </div>
        </div>

        <!-- Draft Campaigns Tab -->
        <div id="draft-campaigns" class="tab-content hidden space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($campaigns->where('status', 'draft') as $campaign)
                    <!-- Same card structure as above -->
                    @include('campaigns.partials.card', ['campaign' => $campaign])
                @endforeach
            </div>
        </div>

        <!-- Closed Campaigns Tab -->
        <div id="closed-campaigns" class="tab-content hidden space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($campaigns->where('status', 'closed') as $campaign)
                    <!-- Same card structure as above -->
                    @include('campaigns.partials.card', ['campaign' => $campaign])
                @endforeach
            </div>
        </div>
    </div>

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
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* Styles pour le zoom de l'image */
        #modalImage {
            transition: transform 0.3s ease;
        }
        
        .image-zoom-trigger:hover {
            transform: scale(1.02);
        }
    </style>

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

        // Tab functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                });

                // Deactivate all tab buttons
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });

                // Activate current tab button
                this.classList.add('active', 'border-blue-500', 'text-blue-600');
                this.classList.remove('border-transparent', 'text-gray-500');

                // Show corresponding tab content
                const tabId = this.textContent.trim().toLowerCase().replace(' ', '-') + '-campaigns';
                document.getElementById(tabId).classList.add('active');
                document.getElementById(tabId).classList.remove('hidden');
            });
        });

        // Dropdown menu functionality
        document.querySelectorAll('[aria-haspopup="true"]').forEach(button => {
            button.addEventListener('click', function() {
                const menu = this.nextElementSibling;
                menu.classList.toggle('hidden');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[aria-haspopup="true"]')) {
                document.querySelectorAll('[role="menu"]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        // Fermer le modal avec la touche Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeImageModal();
            }
        });
    </script>
@endsection

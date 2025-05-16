@extends('layouts.app')

@section('title', 'DigitPosts - Formations & Événements Professionnels')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-16 md:py-24 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
        <div class="container mx-auto px-4 text-center relative z-10">
            <div class="inline-block mb-6">
                <span class="inline-flex items-center rounded-full bg-blue-500 bg-opacity-20 px-6 py-2 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    Formations & Événements Professionnels
                </span>
            </div>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold tracking-tight mb-6">
                Faites Progresser Votre Carrière
            </h1>
            <p class="text-lg md:text-xl text-blue-100 max-w-3xl mx-auto mb-10">
                Plateforme dédiée à la diffusion d'information sur les formations et évènements au Burkina Faso.
                Que vous soyez un Professionnels en quète d'opportunité, un étudiant à la recherche de formations,
                ou une organisation souhaitant partager ses évènements, Diginov vous connecte aux informations
                essentielles pour votre développement.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center max-w-3xl mx-auto">
                <a href="#trainings" class="px-8 py-3 bg-white text-blue-600 rounded-lg text-lg font-semibold hover:bg-blue-50 transition duration-300">
                    Voir les Formations
                </a>
                <a href="#events" class="px-8 py-3 border-2 border-white text-white rounded-lg text-lg font-semibold hover:bg-white hover:bg-opacity-10 transition duration-300">
                    Découvrir les Événements
                </a>
            </div>
        </div>
        <div class="absolute inset-0 overflow-hidden opacity-10">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white to-transparent"></div>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="py-8 bg-white shadow-sm">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="p-4">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $trainings->count() }}</div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Formations</div>
                </div>
                <div class="p-4">
                    <div class="text-3xl font-bold text-purple-600 mb-2">{{ $events->count() }}</div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Événements</div>
                </div>
                <div class="p-4">
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $upcomingCount }}</div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">À venir</div>
                </div>
                <div class="p-4">
                    <div class="text-3xl font-bold text-orange-600 mb-2">{{ $freeCount }}</div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Activités gratuites</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trainings Section -->
    <section id="trainings" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Formations en Vedette</h2>
                <div class="w-20 h-1 bg-blue-600 mx-auto"></div>
            </div>

            @if($trainings->isEmpty())
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">Aucune formation disponible</h3>
                    <p class="text-gray-500 max-w-md mx-auto">Nous n'avons aucune formation programmée pour le moment. Revenez plus tard !</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($trainings as $training)
                        <a href="{{ route('campaigns.show', $training->id) }}" class="group">
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 h-full flex flex-col">
                                @if($training->file)
                                    <div class="h-48 overflow-hidden">
                                        <img src="{{ asset('storage/' . $training->file) }}" alt="{{ $training->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    </div>
                                @else
                                    <div class="h-48 bg-gradient-to-r from-blue-100 to-blue-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                @endif

                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                            Formation
                                        </span>
                                        @if($training->canPaid)
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                ${{ number_format($training->amount, 2) }}
                                            </span>
                                        @else
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                Gratuit
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $training->title }}</h3>
                                    <p class="text-gray-600 mb-4 flex-1">{{ Str::limit($training->description, 120) }}</p>

                                    <div class="flex items-center text-sm text-gray-500 mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($training->start_date)->format('d M, Y') }} -
                                        {{ \Carbon\Carbon::parse($training->end_date)->format('d M, Y') }}
                                    </div>

                                    <div class="flex items-center text-sm text-gray-500 mb-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $training->location }} ({{ $training->place }})
                                    </div>

                                    <div class="mt-auto pt-4 border-t">
                                        <button class="w-full py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                                            Participer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Events Section -->
    <section id="events" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Événements à Venir</h2>
                <div class="w-20 h-1 bg-purple-600 mx-auto"></div>
            </div>

            @if($events->isEmpty())
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-medium text-gray-700 mb-2">Aucun événement programmé</h3>
                    <p class="text-gray-500 max-w-md mx-auto">Revenez bientôt pour découvrir nos prochains événements !</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($events as $event)
                        <a href="{{ route('campaigns.show', $event->id) }}" class="group">
                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border-l-4 border-purple-600 h-full flex flex-col">
                                @if($event->file)
                                    <div class="h-48 overflow-hidden">
                                        <img src="{{ asset('storage/' . $event->file) }}" alt="{{ $event->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                    </div>
                                @else
                                    <div class="h-48 bg-gradient-to-r from-purple-100 to-purple-200 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                            Événement
                                        </span>
                                        @if($event->amount > 0)
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                ${{ number_format($event->amount, 2) }}
                                            </span>
                                        @else
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                Gratuit
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $event->title }}</h3>
                                    <p class="text-gray-600 mb-4 flex-1">{{ Str::limit($event->description, 120) }}</p>

                                    <div class="flex items-center text-sm text-gray-500 mb-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('d M, Y H:i') }}
                                    </div>

                                    <div class="mt-auto pt-4 border-t">
                                        <button class="w-full py-2 bg-purple-600 text-white rounded-md text-sm font-medium hover:bg-purple-700 transition-colors">
                                            Participer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-blue-700 to-blue-900 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Prêt à Organiser Votre Propre Événement ?</h2>
            <p class="text-lg md:text-xl text-blue-100 max-w-3xl mx-auto mb-8">
                Créez votre propre programme de formation ou événement et connectez-vous avec des participants du monde entier.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="#" class="inline-flex items-center px-8 py-3 bg-white text-blue-600 rounded-lg text-lg font-semibold hover:bg-blue-50 transition duration-300">
                        Créer une Activité
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 bg-white text-blue-600 rounded-lg text-lg font-semibold hover:bg-blue-50 transition duration-300">
                        Commencer
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3 border-2 border-white text-white rounded-lg text-lg font-semibold hover:bg-white hover:bg-opacity-10 transition duration-300">
                        Se Connecter
                    </a>
                @endauth
            </div>
        </div>
    </section>
@endsection

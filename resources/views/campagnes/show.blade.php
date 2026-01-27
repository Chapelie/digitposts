@extends('layouts.app')

@section('title', 'Détails')

@section('content')
    <div class="container mx-auto px-4 py-8">
        @if($feed->feedable_type === 'App\Models\Training')
            <!-- Détail d'une formation -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden animate-fade-in-up">
                @if($feed->feedable->file)
                    <div class="h-64 w-full overflow-hidden">
                        <img src="{{ asset('storage/' . $feed->feedable->file) }}" alt="{{ $feed->feedable->title }} - {{ $feed->feedable instanceof \App\Models\Event ? 'Événement' : 'Formation' }} sur DigitPosts" loading="lazy" class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 mb-4 animate-fade-in-left">
                            Formation
                        </span>
                            <h1 class="text-3xl font-bold text-gray-900 animate-fade-in-up">{{ $feed->feedable->title }}</h1>
                        </div>

                        <div class="flex items-center space-x-3">
                            <span class="inline-block px-4 py-2 rounded-full text-lg font-semibold {{ $feed->feedable->is_free ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }} animate-fade-in-right">
                                {{ $feed->feedable->formatted_price }}
                            </span>
                            @auth
                            <button id="favorite-btn" onclick="toggleFavorite('{{ $feed->id }}')" 
                                    class="favorite-btn p-2 rounded-full transition-colors animate-fade-in-right"
                                    data-feed-id="{{ $feed->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                            @endauth
                        </div>
                    </div>

                    <div class="prose max-w-none mb-8 animate-fade-in-up delay-200">
                        {!! nl2br(e($feed->feedable->description)) !!}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-4 rounded-lg animate-fade-in-up delay-300">
                            <h3 class="font-medium text-gray-700 mb-2">Dates</h3>
                            <div class="flex items-center text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Du {{ \Carbon\Carbon::parse($feed->feedable->start_date)->format('d/m/Y') }}
                                au {{ \Carbon\Carbon::parse($feed->feedable->end_date)->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg animate-fade-in-up delay-400">
                            <h3 class="font-medium text-gray-700 mb-2">Lieu</h3>
                            <div class="flex items-center text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $feed->feedable->location }} ({{ $feed->feedable->place }})
                            </div>
                        </div>
                    </div>

                    @if($feed->feedable->link)
                        <div class="mt-6 animate-fade-in-up delay-500">
                            <a href="{{ $feed->feedable->link }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg text-lg font-medium hover:bg-blue-700 transition-colors transform hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                S'inscrire à la formation
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        @elseif($feed->feedable_type === 'App\Models\Event')
            <!-- Détail d'un événement -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-purple-600 animate-fade-in-up">
                @if($feed->feedable->file)
                    <div class="h-64 w-full overflow-hidden">
                        <img src="{{ asset('storage/' . $feed->feedable->file) }}" alt="{{ $feed->feedable->title }} - {{ $feed->feedable instanceof \App\Models\Event ? 'Événement' : 'Formation' }} sur DigitPosts" loading="lazy" class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 mb-4 animate-fade-in-left">
                            Événement
                        </span>
                            <h1 class="text-3xl font-bold text-gray-900 animate-fade-in-up">{{ $feed->feedable->title }}</h1>
                        </div>

                        <div class="flex items-center space-x-3">
                            <span class="inline-block px-4 py-2 rounded-full text-lg font-semibold {{ $feed->feedable->is_free ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }} animate-fade-in-right">
                                {{ $feed->feedable->formatted_price }}
                            </span>
                            @auth
                            <button id="favorite-btn" onclick="toggleFavorite('{{ $feed->id }}')" 
                                    class="favorite-btn p-2 rounded-full transition-colors animate-fade-in-right"
                                    data-feed-id="{{ $feed->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                            @endauth
                        </div>
                    </div>

                    <div class="prose max-w-none mb-8 animate-fade-in-up delay-200">
                        {!! nl2br(e($feed->feedable->description)) !!}
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mb-8 max-w-md animate-fade-in-up delay-300">
                        <h3 class="font-medium text-gray-700 mb-2">Date et heure</h3>
                        <div class="flex items-center text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ \Carbon\Carbon::parse($feed->feedable->start_date)->format('d/m/Y \à H:i') }}
                        </div>
                    </div>

                    @auth
                        @php
                            $userRegistration = \App\Models\Registration::where('user_id', auth()->id())
                                ->where('feed_id', $feed->id)
                                ->first();
                        @endphp
                        
                        @if($userRegistration)
                            <!-- Statut d'inscription -->
                            @if($userRegistration->payment_status === 'paid')
                            <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg animate-fade-in-up delay-400">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h3 class="font-semibold text-green-900">Inscription confirmée</h3>
                                            <p class="text-sm text-green-700">
                                                Votre paiement a été effectué avec succès le 
                                                {{ $userRegistration->payment_date ? \Carbon\Carbon::parse($userRegistration->payment_date)->format('d/m/Y à H:i') : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('user.registrations') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                        Voir mes inscriptions
                                    </a>
                                </div>
                            </div>
                            @elseif($userRegistration->payment_status === 'pending' || $userRegistration->payment_status === 'failed')
                            <div class="mt-6 p-4 {{ $userRegistration->payment_status === 'failed' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' }} border rounded-lg animate-fade-in-up delay-400">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        @if($userRegistration->payment_status === 'failed')
                                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        @else
                                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        @endif
                                        <div>
                                            <h3 class="font-semibold {{ $userRegistration->payment_status === 'failed' ? 'text-red-900' : 'text-yellow-900' }}">
                                                @if($userRegistration->payment_status === 'failed')
                                                    Paiement échoué
                                                @else
                                                    Paiement en attente
                                                @endif
                                            </h3>
                                            <p class="text-sm {{ $userRegistration->payment_status === 'failed' ? 'text-red-700' : 'text-yellow-700' }}">
                                                @if($userRegistration->payment_status === 'failed')
                                                    Le paiement n'a pas pu être effectué. Veuillez réessayer.
                                                @else
                                                    Finalisez votre paiement pour confirmer votre inscription.
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <a href="{{ route('payments.seamless-checkout', $userRegistration->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        {{ $userRegistration->payment_status === 'failed' ? 'Réessayer le paiement' : 'Payer maintenant' }}
                                    </a>
                                </div>
                            </div>
                            @else
                            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg animate-fade-in-up delay-400">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="font-medium text-blue-900">Inscription enregistrée</h3>
                                        <p class="text-sm text-blue-700">
                                            Votre inscription a été enregistrée. @if(!$feed->feedable->is_free) Finalisez le paiement pour confirmer. @endif
                                        </p>
                                    </div>
                                    @if(!$feed->feedable->is_free)
                                    <a href="{{ route('payments.seamless-checkout', $userRegistration->id) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        Payer maintenant
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @else
                            <!-- Bouton d'inscription -->
                            <div class="mt-6 animate-fade-in-up delay-400">
                                <a href="{{ route('inscriptions.create', $feed->id) }}" 
                                   class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg text-lg font-medium hover:bg-purple-700 transition-colors transform hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    @if($feed->feedable->is_free)
                                        S'inscrire gratuitement
                                    @else
                                        S'inscrire et payer
                                    @endif
                                </a>
                            </div>
                        @endif
                    @else
                        <!-- Lien externe pour les visiteurs non connectés -->
                        @if($feed->feedable->link)
                            <div class="mt-6 animate-fade-in-up delay-400">
                                <a href="{{ $feed->feedable->link }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg text-lg font-medium hover:bg-purple-700 transition-colors transform hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    S'inscrire à l'événement
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        @endif

        <!-- Section organisateur -->
        <div class="mt-12 bg-white rounded-xl shadow-md p-6 animate-fade-in-up delay-500">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Organisateur</h2>
            <div class="flex items-center">
                <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                    <span class="text-gray-600 font-medium">{{ substr($feed->user->firstname, 0, 1) }}{{ substr($feed->user->lastname, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">{{ $feed->user->firstname }} {{ $feed->user->lastname }}</h3>
                    <p class="text-sm text-gray-600">Membre depuis {{ $feed->user->created_at->format('m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        
        .animate-fade-in-left {
            animation: fadeInLeft 0.8s ease-out;
        }
        
        .animate-fade-in-right {
            animation: fadeInRight 0.8s ease-out;
        }
        
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        
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
        
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>

    @auth
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifier le statut initial du favori
        const feedId = '{{ $feed->id }}';
        checkFavoriteStatus(feedId);
    });

    function toggleFavorite(feedId) {
        const button = document.querySelector(`[data-feed-id="${feedId}"]`);
        if (!button) {
            console.error('Bouton favori non trouvé');
            return;
        }
        
        const icon = button.querySelector('svg');
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        
        if (!csrfToken) {
            console.error('Token CSRF non trouvé');
            return;
        }
        
        fetch('{{ route("user.toggle-favorite") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify({
                feed_id: feedId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_favorite) {
                    // Ajouté aux favoris
                    icon.style.fill = 'currentColor';
                    icon.style.color = '#dc2626'; // Rouge
                    button.classList.add('text-red-600');
                    button.classList.remove('text-gray-400');
                } else {
                    // Retiré des favoris
                    icon.style.fill = 'none';
                    icon.style.color = '#9ca3af'; // Gris
                    button.classList.remove('text-red-600');
                    button.classList.add('text-gray-400');
                }
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    }

    function checkFavoriteStatus(feedId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('Token CSRF non trouvé');
            return;
        }
        
        fetch('{{ route("user.check-favorite") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify({
                feed_id: feedId
            })
        })
        .then(response => response.json())
        .then(data => {
            const button = document.querySelector(`[data-feed-id="${feedId}"]`);
            if (!button) {
                console.error('Bouton favori non trouvé');
                return;
            }
            
            const icon = button.querySelector('svg');
            if (!icon) {
                console.error('Icône favori non trouvée');
                return;
            }
            
            if (data.is_favorite) {
                icon.style.fill = 'currentColor';
                icon.style.color = '#dc2626'; // Rouge
                button.classList.add('text-red-600');
                button.classList.remove('text-gray-400');
            } else {
                icon.style.fill = 'none';
                icon.style.color = '#9ca3af'; // Gris
                button.classList.remove('text-red-600');
                button.classList.add('text-gray-400');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    }
    </script>
    @endauth
@endsection

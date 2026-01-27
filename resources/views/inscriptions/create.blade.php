@extends('layouts.app')

@section('title', 'Inscription - ' . $feed->feedable->title)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Activity Card with Image -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="md:flex">
                    <!-- Image Section -->
                    @if($feed->feedable->file)
                    <div class="md:w-1/3 h-64 md:h-auto bg-gray-200">
                        <img src="{{ asset('storage/' . $feed->feedable->file) }}" 
                             alt="{{ $feed->feedable->title }}"
                             loading="lazy"
                             class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="md:w-1/3 h-64 md:h-auto bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    @endif
                    
                    <!-- Info Section -->
                    <div class="md:w-2/3 p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $feed->feedable_type === 'App\Models\Training' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ $feed->feedable_type === 'App\Models\Training' ? 'Formation' : 'Événement' }}
                                    </span>
                                    @if($feed->feedable->is_free)
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Gratuit
                                    </span>
                                    @endif
                                </div>
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">{{ $feed->feedable->title }}</h1>
                                
                                @if($feed->feedable->description)
                                <p class="text-gray-600 mb-4 line-clamp-3">{{ Str::limit($feed->feedable->description, 200) }}</p>
                                @endif
                                
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>
                                            @if($feed->feedable_type === 'App\Models\Training' && $feed->feedable->end_date)
                                                Du {{ \Carbon\Carbon::parse($feed->feedable->start_date)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($feed->feedable->end_date)->format('d/m/Y') }}
                                            @else
                                                {{ \Carbon\Carbon::parse($feed->feedable->start_date)->format('d/m/Y à H:i') }}
                                            @endif
                                        </span>
                                    </div>
                                    @if($feed->feedable->location)
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ $feed->feedable->location }}</span>
                                    </div>
                                    @endif
                                    @if($feed->feedable_type === 'App\Models\Training' && $feed->feedable->place)
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>{{ $feed->feedable->place }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <div class="text-3xl font-bold text-blue-600 mb-1">
                                    {{ $feed->feedable->is_free ? 'Gratuit' : $feed->feedable->formatted_price }}
                                </div>
                                @if(!$feed->feedable->is_free)
                                <p class="text-xs text-gray-500">Par participant</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-xl shadow-md p-6 md:p-8">
                <form action="{{ route('inscriptions.store') }}" method="POST" id="registration-form">
                    @csrf
                    <input type="hidden" name="feed_id" value="{{ $feed->id }}">
                    <input type="hidden" name="feed_type" value="{{ $feed->feedable_type }}">

                    <!-- User Information -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                            <input type="text" id="name" name="name" value="{{ $userData['name'] ?? '' }}" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" value="{{ $userData['email'] ?? '' }}" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="tel" id="phone" name="phone" value="{{ $userData['phone'] ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="organization" class="block text-sm font-medium text-gray-700 mb-1">Organisation <span class="text-gray-400 text-xs">(optionnel)</span></label>
                            <input type="text" id="organization" name="organization" value="{{ $userData['organization'] ?? '' }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    @if(!$feed->feedable->is_free)
                        <!-- Information de paiement -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-blue-900 mb-1">Paiement sécurisé</h4>
                                    <p class="text-sm text-blue-700">
                                        Après confirmation de votre inscription, vous serez redirigé vers notre plateforme de paiement sécurisée. 
                                        Vous pourrez choisir votre méthode de paiement (Mobile Money, Carte bancaire, etc.) lors du paiement.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="flex gap-3">
                        <a href="{{ route('campaigns.show', $feed->id) }}" 
                           class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors text-center">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors font-medium">
                            @if($feed->feedable->is_free)
                                Confirmer l'inscription
                            @else
                                Continuer vers le paiement
                            @endif
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registration-form');
        
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Traitement en cours...';
        });
    });
</script>
@endsection

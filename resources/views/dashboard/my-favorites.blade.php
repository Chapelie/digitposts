@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Mes Favoris</h1>
            <p class="text-gray-500">Retrouvez toutes vos activités favorites</p>
        </div>
    </div>

    <!-- Liste des favoris -->
    @if($favorites->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($favorites as $favorite)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <!-- Image -->
                <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                    @if($favorite->feed->feedable->image)
                        <img src="{{ asset('storage/' . $favorite->feed->feedable->file ?? $favorite->feed->feedable->image) }}" loading="lazy" 
                             alt="{{ $favorite->feed->feedable->title }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Contenu -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ class_basename($favorite->feed->feedable_type) }}
                        </span>
                        <button onclick="toggleFavorite('{{ $favorite->feed->id }}')" 
                                class="text-red-600 hover:text-red-800 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                        {{ $favorite->feed->feedable->title }}
                    </h3>

                    <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                        {{ $favorite->feed->feedable->description ?? 'Aucune description disponible.' }}
                    </p>

                    <!-- Informations -->
                    <div class="space-y-2 mb-4">
                        @if($favorite->feed->feedable->start_date)
                        <div class="flex items-center text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $favorite->feed->feedable->start_date->format('d/m/Y H:i') }}
                        </div>
                        @endif

                        @if($favorite->feed->feedable->location)
                        <div class="flex items-center text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $favorite->feed->feedable->location }}
                        </div>
                        @endif

                        @if(isset($favorite->feed->feedable->price))
                        <div class="flex items-center text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                            @if($favorite->feed->feedable->is_free)
                                <span class="text-green-600 font-medium">Gratuit</span>
                            @else
                                {{ number_format($favorite->feed->feedable->price, 0, ',', ' ') }} FCFA
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('campaigns.show', $favorite->feed->id) }}" 
                           class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                            Voir détails
                        </a>
                        @if(!$favorite->feed->feedable->is_free)
                            <button onclick="initiatePayment('{{ $favorite->feed->id }}')" 
                                    class="flex-1 bg-green-600 text-white text-center py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Payer
                            </button>
                        @else
                            <a href="{{ route('inscriptions.create', $favorite->feed->id) }}" 
                               class="flex-1 bg-green-600 text-white text-center py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                                S'inscrire
                            </a>
                        @endif
                    </div>

                    <div class="text-xs text-gray-400 mt-2">
                        Ajouté le {{ $favorite->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun favori</h3>
            <p class="text-gray-500 mb-6">Vous n'avez pas encore ajouté d'activités à vos favoris.</p>
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                Découvrir des activités
            </a>
        </div>
    @endif
</div>

<style>
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
</style>

<script>
function toggleFavorite(feedId) {
    fetch('{{ route("user.toggle-favorite") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            feed_id: feedId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recharger la page pour mettre à jour les favoris
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

function initiatePayment(feedId) {
    // Rediriger vers la page d'inscription qui gère le paiement
    window.location.href = '{{ url("inscriptions") }}/' + feedId + '/create';
}
</script>
@endsection 
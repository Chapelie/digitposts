@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Mon Dashboard</h1>
            <p class="text-gray-500">Bienvenue, {{ Auth::user()->firstname }} ! Voici un aperçu de vos activités</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('user.export-registrations') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exporter
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Inscriptions -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Inscriptions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_registrations'] }}</p>
                </div>
            </div>
        </div>

        <!-- Inscriptions Confirmées -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Confirmées</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['confirmed_registrations'] }}</p>
                </div>
            </div>
        </div>

        <!-- Montant Total Payé -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Payé</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_paid'], 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
        </div>

        <!-- Favoris -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Favoris</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_favorites'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    @if($stats['total_registrations'] > 0)
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-medium text-gray-900">Taux de réussite</h3>
            <span class="text-sm font-medium text-gray-500">{{ $stats['success_rate'] }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $stats['success_rate'] }}%"></div>
        </div>
        <p class="text-sm text-gray-500 mt-2">{{ $stats['confirmed_registrations'] }} sur {{ $stats['total_registrations'] }} inscriptions confirmées</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Inscriptions Récentes -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Inscriptions Récentes</h3>
                    <a href="{{ route('user.registrations') }}" class="text-sm text-blue-600 hover:text-blue-800">Voir tout</a>
                </div>
            </div>
            <div class="p-6">
                @if($recentRegistrations->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentRegistrations as $registration)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $registration->feed->feedable->title }}</h4>
                                <p class="text-sm text-gray-500">{{ class_basename($registration->feed->feedable_type) }}</p>
                                <p class="text-xs text-gray-400">{{ $registration->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($registration->amount_paid > 0)
                                    <span class="text-sm font-medium text-gray-900">{{ number_format($registration->amount_paid, 0, ',', ' ') }} FCFA</span>
                                @endif
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($registration->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($registration->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($registration->status) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500">Aucune inscription récente</p>
                        <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 text-sm">Découvrir des activités</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Favoris Récents -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Favoris Récents</h3>
                    <a href="{{ route('user.favorites') }}" class="text-sm text-blue-600 hover:text-blue-800">Voir tout</a>
                </div>
            </div>
            <div class="p-6">
                @if($recentFavorites->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentFavorites as $favorite)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $favorite->feed->feedable->title }}</h4>
                                <p class="text-sm text-gray-500">{{ class_basename($favorite->feed->feedable_type) }}</p>
                                <p class="text-xs text-gray-400">Ajouté le {{ $favorite->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('campaigns.show', $favorite->feed->id) }}" 
                                   class="text-blue-600 hover:text-blue-800 text-sm">Voir</a>
                                <button onclick="toggleFavorite('{{ $favorite->feed->id }}')" 
                                        class="text-red-600 hover:text-red-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <p class="text-gray-500">Aucun favori</p>
                        <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 text-sm">Découvrir des activités</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Paiements en Attente -->
    @if($stats['pending_payments'] > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <div>
                <h3 class="text-lg font-medium text-yellow-800">Paiements en attente</h3>
                <p class="text-yellow-700">Vous avez {{ number_format($stats['pending_payments'], 0, ',', ' ') }} FCFA de paiements en attente. 
                    <a href="{{ route('user.registrations') }}" class="underline">Voir les détails</a>
                </p>
            </div>
        </div>
    </div>
    @endif

</div>

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
</script>
@endsection 
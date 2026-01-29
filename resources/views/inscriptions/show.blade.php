@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('inscriptions.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour aux inscriptions
        </a>
    </div>

    @php
        $feedable = $registration->feed->feedable ?? null;
        $isEvent = $registration->feed->feedable_type === 'App\Models\Event';
    @endphp

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Header avec image -->
        <div class="relative h-48 bg-gradient-to-r from-blue-600 to-blue-800">
            @if($feedable && $feedable->file)
                <img src="{{ asset('storage/' . $feedable->file) }}" alt="{{ $feedable->title ?? '' }}" class="w-full h-full object-cover opacity-30">
            @endif
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="text-center text-white">
                    <h1 class="text-3xl font-bold mb-2">{{ $feedable->title ?? 'Activité' }}</h1>
                    <p class="text-blue-100">{{ $isEvent ? 'Événement' : 'Formation' }}</p>
                </div>
            </div>
        </div>

        <!-- Détails de l'inscription -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Statut -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Statut de l'inscription</h3>
                    <div class="flex items-center gap-2">
                        @if($registration->status === 'confirmed')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Confirmée</span>
                        @elseif($registration->status === 'pending')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Annulée</span>
                        @endif
                    </div>
                </div>

                <!-- Paiement -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Statut du paiement</h3>
                    <div class="flex items-center gap-2">
                        @if($registration->payment_status === 'paid')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Payé</span>
                            <span class="text-gray-600">{{ number_format($registration->amount_paid ?? 0, 0, ',', ' ') }} XOF</span>
                        @elseif($registration->payment_status === 'pending')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                        @else
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Échoué</span>
                        @endif
                    </div>
                </div>

                <!-- Date d'inscription -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Date d'inscription</h3>
                    <p class="text-gray-900">{{ $registration->created_at->format('d/m/Y à H:i') }}</p>
                </div>

                <!-- Date de l'activité -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Date de l'activité</h3>
                    <p class="text-gray-900">
                        @if($feedable)
                            {{ $feedable->start_date ? $feedable->start_date->format('d/m/Y à H:i') : 'Non définie' }}
                        @else
                            Non disponible
                        @endif
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex flex-wrap gap-3">
                @if($feedable)
                    <a href="{{ route('campaigns.show', $registration->feed->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Voir l'activité
                    </a>
                @endif

                @if($registration->payment_status === 'paid')
                    <a href="{{ route('receipts.download', $registration->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Télécharger le reçu
                    </a>
                    <a href="{{ route('receipts.show', $registration->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Voir le reçu
                    </a>
                @elseif($registration->payment_status === 'pending' && $feedable && ($feedable->amount ?? 0) > 0)
                    <a href="{{ route('payments.seamless-checkout', $registration->id) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Effectuer le paiement
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($registration->notes)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes</h3>
            <p class="text-gray-600">{{ $registration->notes }}</p>
        </div>
    @endif
</div>
@endsection

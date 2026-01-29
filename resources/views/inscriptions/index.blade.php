@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Mes Inscriptions</h1>
            <p class="text-gray-500">Historique de toutes vos inscriptions</p>
        </div>
        <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Découvrir plus d'activités
        </a>
    </div>

    @if($registrations->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune inscription</h3>
            <p class="text-gray-600 mb-6">Vous n'avez pas encore d'inscriptions. Découvrez nos formations et événements.</p>
            <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                Découvrir les activités
            </a>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="divide-y divide-gray-200">
                @foreach($registrations as $registration)
                    @php
                        $feedable = $registration->feed->feedable ?? null;
                        $isEvent = $registration->feed->feedable_type === 'App\Models\Event';
                    @endphp
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-4">
                            @if($feedable && $feedable->file)
                                <img src="{{ asset('storage/' . $feedable->file) }}" alt="{{ $feedable->title ?? 'Image' }}" class="w-20 h-20 rounded-lg object-cover">
                            @else
                                <div class="w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $feedable->title ?? 'Activité supprimée' }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            {{ $isEvent ? 'Événement' : 'Formation' }} - Inscrit le {{ $registration->created_at->format('d/m/Y à H:i') }}
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        @if($registration->status === 'confirmed')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Confirmée</span>
                                        @elseif($registration->status === 'pending')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Annulée</span>
                                        @endif
                                        
                                        @if($registration->payment_status === 'paid')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Payé</span>
                                        @elseif($registration->payment_status === 'pending')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Paiement en attente</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex items-center gap-4">
                                    @if($feedable)
                                        <a href="{{ route('campaigns.show', $registration->feed->id) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                            Voir l'activité
                                        </a>
                                    @endif
                                    
                                    @if($registration->payment_status === 'paid')
                                        <a href="{{ route('receipts.download', $registration->id) }}" class="text-sm text-green-600 hover:text-green-800">
                                            Télécharger le reçu
                                        </a>
                                    @elseif($registration->payment_status === 'pending' && $feedable && $feedable->amount > 0)
                                        <a href="{{ route('payments.seamless-checkout', $registration->id) }}" class="text-sm text-orange-600 hover:text-orange-800">
                                            Effectuer le paiement
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-4">
            {{ $registrations->links() }}
        </div>
    @endif
</div>
@endsection

@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 p-4 text-green-800 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-red-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Mes Inscriptions</h1>
            <p class="text-gray-500">Gérez toutes vos inscriptions et paiements</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('user.export-registrations') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Exporter en PDF
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    @php
        $totalRegistrations = $registrations->total();
        $confirmedCount = $registrations->filter(fn($r) => $r->status === 'confirmed')->count();
        $pendingCount = $registrations->filter(fn($r) => $r->status === 'pending')->count();
        $paidCount = $registrations->filter(fn($r) => $r->payment_status === 'paid')->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $totalRegistrations }}</div>
                    <div class="text-sm text-gray-500">Total</div>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $confirmedCount }}</div>
                    <div class="text-sm text-gray-500">Confirmées</div>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</div>
                    <div class="text-sm text-gray-500">En attente</div>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-2xl font-bold text-gray-900">{{ $paidCount }}</div>
                    <div class="text-sm text-gray-500">Payées</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-4">
        <div class="flex flex-wrap gap-2">
            <button onclick="filterRegistrations('all')" 
                    class="filter-btn active px-4 py-2 rounded-md text-sm font-medium bg-blue-600 text-white">
                Toutes
            </button>
            <button onclick="filterRegistrations('confirmed')" 
                    class="filter-btn px-4 py-2 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                Confirmées
            </button>
            <button onclick="filterRegistrations('pending')" 
                    class="filter-btn px-4 py-2 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                En attente
            </button>
            <button onclick="filterRegistrations('cancelled')" 
                    class="filter-btn px-4 py-2 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                Annulées
            </button>
            <button onclick="filterRegistrations('paid')" 
                    class="filter-btn px-4 py-2 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                Payées
            </button>
            <button onclick="filterRegistrations('unpaid')" 
                    class="filter-btn px-4 py-2 rounded-md text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">
                Non payées
            </button>
        </div>
    </div>

    <!-- Liste des inscriptions -->
    @if($registrations->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($registrations as $registration)
            <div class="registration-card rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden" 
                 data-status="{{ $registration->status }}" 
                 data-payment-status="{{ $registration->payment_status }}">
                <!-- Image -->
                <div class="h-48 bg-gray-200 overflow-hidden">
                    @if($registration->feed->feedable->file)
                        <img src="{{ asset('storage/' . $registration->feed->feedable->file) }}" loading="lazy" 
                             alt="{{ $registration->feed->feedable->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Contenu -->
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $registration->feed->feedable_type === 'App\Models\Training' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ $registration->feed->feedable_type === 'App\Models\Training' ? 'Formation' : 'Événement' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($registration->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($registration->status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            @if($registration->status === 'confirmed')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            {{ ucfirst($registration->status) }}
                        </span>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                        {{ $registration->feed->feedable->title }}
                    </h3>

                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                        {{ Str::limit($registration->feed->feedable->description ?? 'Aucune description disponible.', 100) }}
                    </p>

                    <!-- Informations -->
                    <div class="space-y-2 mb-4">
                        @if($registration->feed->feedable->start_date)
                        <div class="flex items-center text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="truncate">
                                @if($registration->feed->feedable_type === 'App\Models\Training' && $registration->feed->feedable->end_date)
                                    {{ \Carbon\Carbon::parse($registration->feed->feedable->start_date)->format('d/m/y') }} → {{ \Carbon\Carbon::parse($registration->feed->feedable->end_date)->format('d/m/y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($registration->feed->feedable->start_date)->format('d/m/y H:i') }}
                                @endif
                            </span>
                        </div>
                        @endif

                        @if($registration->feed->feedable->location)
                        <div class="flex items-center text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate">{{ $registration->feed->feedable->location }}</span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div class="text-sm">
                                <span class="text-gray-500">Montant:</span>
                                <span class="font-semibold text-gray-900 ml-1">
                                    @if($registration->amount_paid > 0)
                                        {{ number_format($registration->amount_paid, 0, ',', ' ') }} FCFA
                                    @else
                                        <span class="text-green-600">Gratuit</span>
                                    @endif
                                </span>
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $registration->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <!-- Statut de paiement -->
                    @if(!$registration->feed->feedable->is_free)
                    <div class="mb-4 p-3 rounded-lg 
                        @if($registration->payment_status === 'paid') bg-green-50 border border-green-200
                        @elseif($registration->payment_status === 'pending') bg-yellow-50 border border-yellow-200
                        @elseif(in_array($registration->payment_status, ['failed', 'cancelled'])) bg-red-50 border border-red-200
                        @else bg-gray-50 border border-gray-200 @endif">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                @if($registration->payment_status === 'paid')
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <span class="text-sm font-medium text-green-800 block">Paiement effectué</span>
                                        @if($registration->payment_date)
                                        <span class="text-xs text-green-600">Le {{ \Carbon\Carbon::parse($registration->payment_date)->format('d/m/Y à H:i') }}</span>
                                        @endif
                                    </div>
                                @elseif($registration->payment_status === 'pending')
                                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <span class="text-sm font-medium text-yellow-800 block">Paiement en attente</span>
                                        <span class="text-xs text-yellow-600">Finalisez votre paiement</span>
                                    </div>
                                @elseif(in_array($registration->payment_status, ['failed', 'cancelled']))
                                    <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <span class="text-sm font-medium text-red-800 block">{{ $registration->payment_status === 'failed' ? 'Paiement échoué' : 'Paiement annulé' }}</span>
                                        <span class="text-xs text-red-600">Vous pouvez réessayer</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-600">Paiement requis</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="mb-4 p-3 rounded-lg bg-gray-50 border border-gray-200">
                        <span class="text-sm text-gray-600">Activité gratuite</span>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex flex-col space-y-2">
                        <a href="{{ route('campaigns.show', $registration->feed->id) }}" 
                           class="w-full bg-blue-600 text-white text-center py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                            Voir les détails
                        </a>

                        @if(!$registration->feed->feedable->is_free && $registration->payment_status === 'paid')
                            <div class="flex flex-wrap gap-2 pt-2 border-t border-gray-200">
                                <a href="{{ route('receipts.show', $registration->id) }}" target="_blank" rel="noopener"
                                   class="flex-1 min-w-[120px] inline-flex items-center justify-center py-2 px-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Voir le reçu
                                </a>
                                <a href="{{ route('receipts.download', $registration->id) }}"
                                   class="flex-1 min-w-[120px] inline-flex items-center justify-center py-2 px-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Télécharger
                                </a>
                                <form action="{{ route('receipts.send-email', $registration->id) }}" method="post" class="flex-1 min-w-[120px]">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center py-2 px-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        Envoyer par mail
                                    </button>
                                </form>
                            </div>
                        @endif
                        
                        @if(!$registration->feed->feedable->is_free)
                            @if($registration->payment_status === 'pending' || in_array($registration->payment_status, ['failed', 'cancelled']))
                                <a href="{{ route('payments.seamless-checkout', $registration->id) }}" 
                                   class="w-full {{ $registration->payment_status === 'pending' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-red-600 hover:bg-red-700' }} text-white text-center py-2 px-4 rounded-md transition-colors flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    {{ $registration->payment_status === 'pending' ? 'Payer maintenant' : 'Réessayer le paiement' }}
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $registrations->links() }}
        </div>
    @else
        <div class="text-center py-12 rounded-xl border border-gray-200 bg-white shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune inscription</h3>
            <p class="text-gray-500 mb-6">Vous n'avez pas encore d'inscriptions. Découvrez nos activités !</p>
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                Découvrir des activités
            </a>
        </div>
    @endif
</div>

<style>
.filter-btn.active {
    @apply bg-blue-600 text-white;
}

.registration-card.hidden {
    display: none;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<script>
function filterRegistrations(filter) {
    // Mettre à jour les boutons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'hover:bg-gray-50');
    });
    
    event.target.classList.add('active', 'bg-blue-600', 'text-white');
    event.target.classList.remove('text-gray-700', 'bg-white', 'border', 'border-gray-300', 'hover:bg-gray-50');
    
    // Filtrer les cartes
    const cards = document.querySelectorAll('.registration-card');
    cards.forEach(card => {
        const status = card.dataset.status;
        const paymentStatus = card.dataset.paymentStatus;
        
        let show = false;
        
        if (filter === 'all') {
            show = true;
        } else if (filter === 'confirmed' && status === 'confirmed') {
            show = true;
        } else if (filter === 'pending' && status === 'pending') {
            show = true;
        } else if (filter === 'cancelled' && status === 'cancelled') {
            show = true;
        } else if (filter === 'paid' && paymentStatus === 'paid') {
            show = true;
        } else if (filter === 'unpaid' && paymentStatus !== 'paid' && paymentStatus !== null) {
            show = true;
        }
        
        if (show) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}
</script>
@endsection

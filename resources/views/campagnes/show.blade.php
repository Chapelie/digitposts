@extends('layouts.app')

@section('title', 'Détails')

@section('content')
    <div class="container mx-auto px-4 py-8">
        @if($feed->feedable_type === 'App\Models\Training')
            <!-- Détail d'une formation -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                @if($feed->feedable->file)
                    <div class="h-64 w-full overflow-hidden">
                        <img src="{{ asset('storage/' . $feed->feedable->file) }}" alt="{{ $feed->feedable->title }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 mb-4">
                            Formation
                        </span>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $feed->feedable->title }}</h1>
                        </div>

                        @if($feed->feedable->canPaid)
                            <span class="inline-block px-4 py-2 rounded-full text-lg font-semibold bg-green-100 text-green-800">
                            {{ number_format($feed->feedable->amount, 2) }} €
                        </span>
                        @else
                            <span class="inline-block px-4 py-2 rounded-full text-lg font-semibold bg-gray-100 text-gray-800">
                            Gratuit
                        </span>
                        @endif
                    </div>

                    <div class="prose max-w-none mb-8">
                        {!! nl2br(e($feed->feedable->description)) !!}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-medium text-gray-700 mb-2">Dates</h3>
                            <div class="flex items-center text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Du {{ \Carbon\Carbon::parse($feed->feedable->start_date)->format('d/m/Y') }}
                                au {{ \Carbon\Carbon::parse($feed->feedable->end_date)->format('d/m/Y') }}
                            </div>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
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
                        <div class="mt-6">
                            <a href="{{ $feed->feedable->link }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg text-lg font-medium hover:bg-blue-700 transition-colors">
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
            <div class="bg-white rounded-xl shadow-md overflow-hidden border-l-4 border-purple-600">
                @if($feed->feedable->file)
                    <div class="h-64 w-full overflow-hidden">
                        <img src="{{ asset('storage/' . $feed->feedable->file) }}" alt="{{ $feed->feedable->title }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 mb-4">
                            Événement
                        </span>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $feed->feedable->title }}</h1>
                        </div>

                        @if($feed->feedable->amount > 0)
                            <span class="inline-block px-4 py-2 rounded-full text-lg font-semibold bg-green-100 text-green-800">
                            {{ number_format($feed->feedable->amount, 2) }} €
                        </span>
                        @else
                            <span class="inline-block px-4 py-2 rounded-full text-lg font-semibold bg-gray-100 text-gray-800">
                            Gratuit
                        </span>
                        @endif
                    </div>

                    <div class="prose max-w-none mb-8">
                        {!! nl2br(e($feed->feedable->description)) !!}
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mb-8 max-w-md">
                        <h3 class="font-medium text-gray-700 mb-2">Date et heure</h3>
                        <div class="flex items-center text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ \Carbon\Carbon::parse($feed->feedable->start_date)->format('d/m/Y \à H:i') }}
                        </div>
                    </div>

                    @if($feed->feedable->link)
                        <div class="mt-6">
                            <a href="{{ $feed->feedable->link }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg text-lg font-medium hover:bg-purple-700 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                S'inscrire à l'événement
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Section organisateur -->
        <div class="mt-12 bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Organisateur</h2>
            <div class="flex items-center">
                <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                    <span class="text-gray-600 font-medium">{{ substr($feed->user->name, 0, 1) }}</span>
                </div>
                <div>
                    <h3 class="font-medium text-gray-900">{{ $feed->user->name }}</h3>
                    <p class="text-sm text-gray-600">Membre depuis {{ $feed->user->created_at->format('m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

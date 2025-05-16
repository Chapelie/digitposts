@extends('layouts.dashboard')

@section('content')
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                <p class="text-gray-500">Bienvenue, {{ $user->name }}! voici un aperçu de vos campagnes.</p>
            </div>
            <a href="/dashboard/campaigns/new" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
              Nouvelle campagne
            </a>
        </div>

        <!-- Stats Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Total Campaigns -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Total des campagnes</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $totalCampaigns }}</div>
                    <p class="text-xs text-gray-500">Active campaigns</p>
                </div>
            </div>

            <!-- Total Registrations -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Inscription totale</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $totalRegistrations }}</div>
                    <p class="text-xs text-gray-500">Total des participants</p>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Evenement à venir</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">{{ $upcomingCampaigns->count() }}</div>
                    @if($upcomingCampaigns->isNotEmpty())
                        <p class="text-xs text-gray-500">Next in {{ $upcomingCampaigns->first()->start_date->diffForHumans() }}</p>
                    @else
                        <p class="text-xs text-gray-500">Aucun évènements à venir</p>
                    @endif
                </div>
            </div>

            <!-- Completion Rate -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Taux d'achèvement</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
                        <polyline points="16 7 22 7 22 13"></polyline>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">92%</div>
                    <p class="text-xs text-gray-500">Taux d'achèvement moyen</p>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div>
            <div class="flex space-x-2 mb-4">
                <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-blue-600 text-white">
                    À venir
                </button>
                <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-white text-gray-700 hover:bg-gray-100">
                    En cours
                </button>
                <button class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-white text-gray-700 hover:bg-gray-100">
                    Passer
                </button>
            </div>

            <!-- Upcoming Content -->
            <div class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($upcomingCampaigns as $campaign)
                        <div class="rounded-xl border bg-white shadow-sm">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold">{{ $campaign->title }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $campaign->type }} •
                                    @if(isset($campaign->end_date))
                                        {{ $campaign->start_date->format('M d') }} - {{ $campaign->end_date->format('d, Y') }}
                                    @else
                                        {{ $campaign->start_date->format('M d, Y') }}
                                    @endif
                                </p>
                            </div>
                            <div class="p-6 pt-0">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        <span class="text-sm text-gray-500">{{ $campaign->registrations->count() }} Inscription</span>
                                    </div>
                                    <a href="/dashboard/campaigns/{{ $campaign->id }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        Gérer
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Activités récentes</h2>
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium text-blue-600 hover:text-blue-800 focus:outline-none">
                    Voir tout
                </button>
            </div>
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="divide-y">
                    @foreach($feeds as $feed)
                        <div class="flex items-center gap-4 p-4">
                            <div class="bg-blue-100 p-2 rounded-full">
                                @if($feed->feedable_type === 'App\Models\Training')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">
                                    @if($feed->status === 'published')
                                        New {{ class_basename($feed->feedable_type) }} published
                                    @else
                                        {{ class_basename($feed->feedable_type) }} updated
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">{{ $feed->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

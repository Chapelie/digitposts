@extends('layouts.dashboard')

@section('content')
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Dashboard Administrateur</h1>
                <p class="text-gray-500">Vue d'ensemble de la plateforme DigiPosts</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.users') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Gérer les utilisateurs
                </a>
                <a href="{{ route('admin.activities') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Gérer les activités
                </a>
                <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Plans (montants)
                </a>
                <a href="{{ route('admin.subscriptions.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Gérer les abonnements
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            @if(config('digitposts.show_users_count', false))
            <!-- Total Users (masqué par défaut) -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Total utilisateurs</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold text-blue-600">{{ $totalUsers }}</div>
                    <p class="text-xs text-gray-500">Utilisateurs inscrits</p>
                </div>
            </div>
            @endif

            <!-- Total Trainings -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Total formations</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold text-green-600">{{ $totalTrainings }}</div>
                    <p class="text-xs text-gray-500">Formations créées</p>
                </div>
            </div>

            <!-- Total Events -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Total événements</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold text-purple-600">{{ $totalEvents }}</div>
                    <p class="text-xs text-gray-500">Événements créés</p>
                </div>
            </div>

            <!-- Total Registrations -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Total inscriptions</h3>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 12l2 2 4-4"></path>
                        <path d="M21 12c-1 0-2-1-2-2s1-2 2-2 2 1 2 2-1 2-2 2z"></path>
                        <path d="M3 12c1 0 2-1 2-2s-1-2-2-2-2 1-2 2 1 2 2 2z"></path>
                        <path d="M12 3c0 1-1 2-2 2s-2-1-2-2 1-2 2-2 2 1 2 2z"></path>
                        <path d="M12 21c0-1 1-2 2-2s2 1 2 2-1 2-2 2-2-1-2-2z"></path>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold text-orange-600">{{ $totalRegistrations }}</div>
                    <p class="text-xs text-gray-500">Inscriptions totales</p>
                </div>
            </div>
        </div>

        <!-- KPI Business -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Abonnements actifs</h3>
                    <span class="text-xs text-gray-400">(% users)</span>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold text-blue-600">{{ $kpis['subscriptionsActive'] }}</div>
                    <p class="text-xs text-gray-500">{{ $kpis['subscriptionPenetration'] }}% des utilisateurs ({{ $kpis['activeSubscribersUsers'] }})</p>
                </div>
            </div>

            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Revenus total</h3>
                    <span class="text-xs text-gray-400">subs + inscriptions</span>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold text-green-600">{{ number_format((float)$kpis['totalRevenueTotal'], 0, ',', ' ') }} XOF</div>
                    <p class="text-xs text-gray-500">Abonnements: {{ number_format((float)$kpis['subscriptionsRevenueTotal'], 0, ',', ' ') }} • Inscriptions: {{ number_format((float)$kpis['registrationsRevenueTotal'], 0, ',', ' ') }}</p>
                </div>
            </div>

            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Revenus (30 jours)</h3>
                    <span class="text-xs text-gray-400">tendance</span>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format((float)$kpis['totalRevenue30d'], 0, ',', ' ') }} XOF</div>
                    <p class="text-xs text-gray-500">Subs: {{ number_format((float)$kpis['subscriptionsRevenue30d'], 0, ',', ' ') }} • Inscriptions: {{ number_format((float)$kpis['registrationsRevenue30d'], 0, ',', ' ') }}</p>
                </div>
            </div>

            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Inscriptions payées</h3>
                    <span class="text-xs text-gray-400">conversion</span>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold text-orange-600">{{ $kpis['registrationsPaidTotal'] }}</div>
                    <p class="text-xs text-gray-500">{{ $kpis['registrationPaidRate'] }}% des inscriptions</p>
                </div>
            </div>
        </div>

        <!-- KPI Contenu -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Publications publiées</h3>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold text-blue-600">{{ $kpis['publishedFeeds'] }}</div>
                    <p class="text-xs text-gray-500">Brouillons: {{ $kpis['draftFeeds'] }}</p>
                </div>
            </div>

            <div class="rounded-xl border bg-white shadow-sm">
                <div class="flex flex-row items-center justify-between p-6 pb-2">
                    <h3 class="text-sm font-medium">Total abonnements</h3>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold text-green-600">{{ $kpis['subscriptionsTotal'] }}</div>
                    <p class="text-xs text-gray-500">Historique</p>
                </div>
            </div>

            <div class="rounded-xl border bg-white shadow-sm p-6 md:col-span-2 lg:col-span-2">
                <h3 class="text-sm font-medium mb-2">Accès rapide</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.subscriptions.index') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Abonnements</a>
                    <a href="{{ route('admin.users') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Utilisateurs</a>
                    <a href="{{ route('admin.activities') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Activités</a>
                    <a href="{{ route('admin.registrations') }}" class="rounded-md border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">Inscriptions</a>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Monthly Statistics -->
            <div class="rounded-xl border bg-white shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Statistiques mensuelles</h3>
                <div class="space-y-3">
                    @foreach($monthlyStats as $month => $stats)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium">{{ $month }}</span>
                            <div class="flex gap-4 text-xs">
                                <span class="text-blue-600">{{ $stats['users'] }} utilisateurs</span>
                                <span class="text-green-600">{{ $stats['trainings'] }} formations</span>
                                <span class="text-purple-600">{{ $stats['events'] }} événements</span>
                                <span class="text-orange-600">{{ $stats['registrations'] }} inscriptions</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Creators -->
            <div class="rounded-xl border bg-white shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-4">Top créateurs</h3>
                <div class="space-y-3">
                    @foreach($topCreators as $creator)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium">{{ $creator->name }}</p>
                                <p class="text-xs text-gray-500">{{ $creator->email }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium">{{ $creator->feeds_count }} activités</p>
                                <p class="text-xs text-gray-500">{{ $creator->registrations_count }} inscriptions</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Recent Users -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Utilisateurs récents</h3>
                </div>
                <div class="divide-y">
                    @foreach($recentUsers as $user)
                        <div class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                                <span class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Activités récentes</h3>
                </div>
                <div class="divide-y">
                    @foreach($recentFeeds as $feed)
                        <div class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-green-100 p-2 rounded-full">
                                    @if($feed->feedable_type === 'App\Models\Training')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">{{ $feed->feedable->title }}</p>
                                    <p class="text-xs text-gray-500">par {{ $feed->user->name }}</p>
                                </div>
                                <span class="text-xs text-gray-400">{{ $feed->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Registrations -->
            <div class="rounded-xl border bg-white shadow-sm">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold">Inscriptions récentes</h3>
                </div>
                <div class="divide-y">
                    @foreach($recentRegistrations as $registration)
                        <div class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-orange-100 p-2 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 12l2 2 4-4"></path>
                                        <path d="M21 12c-1 0-2-1-2-2s1-2 2-2 2 1 2 2-1 2-2 2z"></path>
                                        <path d="M3 12c1 0 2-1 2-2s-1-2-2-2-2 1-2 2 1 2 2 2z"></path>
                                        <path d="M12 3c0 1-1 2-2 2s-2-1-2-2 1-2 2-2 2 1 2 2z"></path>
                                        <path d="M12 21c0-1 1-2 2-2s2 1 2 2-1 2-2 2-2-1-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium">{{ $registration->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $registration->feed->feedable->title }}</p>
                                </div>
                                <span class="text-xs text-gray-400">{{ $registration->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Popular Activities -->
        <div class="rounded-xl border bg-white shadow-sm">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold">Activités populaires</h3>
            </div>
            <div class="divide-y">
                @foreach($popularActivities as $activity)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="bg-purple-100 p-2 rounded-full">
                                    @if($activity->feedable_type === 'App\Models\Training')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                            <line x1="16" y1="2" x2="16" y2="6"></line>
                                            <line x1="8" y1="2" x2="8" y2="6"></line>
                                            <line x1="3" y1="10" x2="21" y2="10"></line>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium">{{ $activity->feedable->title }}</p>
                                    <p class="text-sm text-gray-500">{{ $activity->feedable_type === 'App\Models\Training' ? 'Formation' : 'Événement' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-purple-600">{{ $activity->registrations_count }}</p>
                                <p class="text-xs text-gray-500">inscriptions</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection 
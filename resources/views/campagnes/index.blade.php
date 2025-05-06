@extends('layouts.dashboard')

@section('content')
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Mes Campagnes</h1>
                <p class="text-gray-500">Gérez vos programmes de formation et événements</p>
            </div>
            <a href="{{ route('campaigns.create') }}" class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Nouvelle Campagne
            </a>
        </div>

        <!-- Filters Section -->
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" placeholder="Rechercher des campagnes..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm pl-9">
            </div>

            <!-- Type Filter -->
            <select class="block w-full md:w-[180px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="all">Tous les types</option>
                <option value="training">Formation</option>
                <option value="event">Événement</option>
            </select>

            <!-- Status Filter -->
            <select class="block w-full md:w-[180px] rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="all">Tous les statuts</option>
                <option value="published">Publié</option>
                <option value="draft">Brouillon</option>
                <option value="closed">Terminé</option>
            </select>
        </div>

        <!-- Tabs Navigation -->
        <div class="border-b">
            <nav class="-mb-px flex space-x-8">
                <button type="button" class="tab-button active border-blue-500 text-blue-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Toutes les campagnes
                </button>
                <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Publiées
                </button>
                <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Brouillons
                </button>
                <button type="button" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Terminées
                </button>
            </nav>
        </div>

        <!-- All Campaigns Tab -->
        <div id="all-campaigns" class="tab-content active space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($campaigns as $campaign)
                    <div class="rounded-lg border bg-white shadow-sm">
                        <!-- Card Header -->
                        <div class="p-6 pb-2">
                            <div class="flex justify-between items-start">
                                <!-- Status Badge -->
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ">
                {{ $campaign->status }}
              </span>

                                <!-- Dropdown Menu -->
                                <div class="relative">
                                    <button type="button" class="inline-flex items-center rounded-full p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="12" cy="5" r="1"></circle>
                                            <circle cx="12" cy="19" r="1"></circle>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Items -->
                                    <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none hidden" role="menu">
                                        <div class="py-1">
                                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                Voir
                                            </a>
                                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                </svg>
                                                Modifier
                                            </a>
                                            <form method="POST" action="#" class="block w-full">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100" role="menuitem">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                    Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Campaign Title -->
                            <h3 class="mt-2 text-lg font-semibold truncate">{{ $campaign->title }}</h3>

                            <!-- Campaign Meta -->
                            <div class="flex items-center text-sm text-gray-500 mt-1">
                                <span>{{ $campaign->type }}</span>
                                <span class="mx-1">•</span>
                                <span class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                {{ $campaign->date_range }}
              </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="px-6 pb-2">
                            <div class="text-sm">
                                <span class="font-medium">{{ $campaign->registrations_count }}</span> inscriptions
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="p-6 pt-0">
                            <div class="flex gap-2 w-full">
                                <a href="#" class="flex-1">
                                    <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        Modifier
                                    </button>
                                </a>

                                @if($campaign->status === 'published')
                                    <a href="{{ route('campaigns.registrations.index', $campaign->id) }}" class="flex-1">
                                        <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full">
                                            Gérer
                                        </button>
                                    </a>
                                @elseif($campaign->status === 'draft')
                                    <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full">
                                        Publier
                                    </button>
                                @else
                                    <a href="#" class="flex-1">
                                        <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 w-full">
                                            Rapport
                                        </button>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Published Campaigns Tab -->
        <div id="published-campaigns" class="tab-content hidden space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($campaigns->where('status', 'published') as $campaign)
                    <!-- Same card structure as above -->
                    @include('campaigns.partials.card', ['campaign' => $campaign])
                @endforeach
            </div>
        </div>

        <!-- Draft Campaigns Tab -->
        <div id="draft-campaigns" class="tab-content hidden space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($campaigns->where('status', 'draft') as $campaign)
                    <!-- Same card structure as above -->
                    @include('campaigns.partials.card', ['campaign' => $campaign])
                @endforeach
            </div>
        </div>

        <!-- Closed Campaigns Tab -->
        <div id="closed-campaigns" class="tab-content hidden space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach($campaigns->where('status', 'closed') as $campaign)
                    <!-- Same card structure as above -->
                    @include('campaigns.partials.card', ['campaign' => $campaign])
                @endforeach
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                });

                // Deactivate all tab buttons
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });

                // Activate current tab button
                this.classList.add('active', 'border-blue-500', 'text-blue-600');
                this.classList.remove('border-transparent', 'text-gray-500');

                // Show corresponding tab content
                const tabId = this.textContent.trim().toLowerCase().replace(' ', '-') + '-campaigns';
                document.getElementById(tabId).classList.add('active');
                document.getElementById(tabId).classList.remove('hidden');
            });
        });

        // Dropdown menu functionality
        document.querySelectorAll('[aria-haspopup="true"]').forEach(button => {
            button.addEventListener('click', function() {
                const menu = this.nextElementSibling;
                menu.classList.toggle('hidden');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[aria-haspopup="true"]')) {
                document.querySelectorAll('[role="menu"]').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });
    </script>
@endsection

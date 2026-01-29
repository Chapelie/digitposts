<!-- Composant mobile-nav -->
<div class="md:hidden fixed bottom-0 left-0 right-0 border-t border-gray-200 bg-white shadow-sm z-50" id="mobile-nav">
    <div class="flex items-center justify-around h-16">
        <!-- Accueil -->
        <a href="/" class="nav-item flex flex-col items-center justify-center w-full h-full" data-page="home">
            <svg class="h-5 w-5 icon-home" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            <span class="text-xs mt-1 label-home">Accueil</span>
        </a>

        <!-- Filtres -->
        <button id="mobile-filters-btn" class="nav-item flex flex-col items-center justify-center w-full h-full transition-transform duration-150" data-page="filters" onclick="document.getElementById('filter').scrollIntoView({behavior: 'smooth'}); this.classList.add('scale-110','bg-blue-50'); setTimeout(()=>this.classList.remove('scale-110','bg-blue-50'),300);">
            <svg class="h-5 w-5 icon-filters" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"></polygon>
            </svg>
            <span class="text-xs mt-1 label-filters">Filtres</span>
        </button>

        <!-- Création -->
        <a href="{{ route('dashboard.campaigns.create') }}" class="nav-item flex flex-col items-center justify-center w-full h-full" data-page="create">
            <div class="bg-blue-600 text-white p-2 rounded-full">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="M12 5v14" />
                </svg>
            </div>
            <span class="text-xs mt-1 text-muted-foreground">Créer</span>
        </a>

        <!-- Profil/Dashboard -->
        @auth
            <a href="{{ route('user.profile') }}" class="nav-item flex flex-col items-center justify-center w-full h-full" data-page="profile">
                <svg class="h-5 w-5 icon-profile" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                <span class="text-xs mt-1 label-profile">Profil</span>
            </a>
        @else
            <a href="/login" class="nav-item flex flex-col items-center justify-center w-full h-full" data-page="login">
                <svg class="h-5 w-5 icon-login" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                </svg>
                <span class="text-xs mt-1 label-login">Connexion</span>
            </a>
        @endauth
    </div>
</div>

<!-- Mobile Filters Overlay -->
<div id="mobile-filters-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="absolute bottom-16 left-0 right-0 bg-white rounded-t-2xl border-t border-gray-200 shadow-sm transform transition-transform duration-300">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Filtrer par Catégorie</h3>
                <button id="close-filters" class="p-2 rounded-full hover:bg-gray-100 border border-gray-200">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-2">
                <a href="{{ route('home') }}" 
                   class="block px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ !request('category') ? 'bg-blue-100 text-blue-700' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">
                    Toutes les activités
                </a>
                @foreach($categories ?? [] as $category)
                    <a href="{{ route('home', ['category' => $category->id]) }}" 
                       class="block px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ request('category') == $category->id ? 'bg-blue-100 text-blue-700' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const currentPath = window.location.pathname;
        let activePage = 'home';

        if (currentPath === '/') activePage = 'home';
        else if (currentPath.includes('/create')) activePage = 'create';
        else if (currentPath.includes('/login')) activePage = 'login';
        else if (currentPath.includes('/profile') || currentPath.includes('/dashboard/settings')) activePage = 'profile';
        else if (currentPath.includes('/dashboard')) activePage = 'dashboard';

        const navItems = document.querySelectorAll('.nav-item');

        navItems.forEach(item => {
            if (item.dataset.page === activePage) {
                markAsActive(item);
            }

            item.addEventListener('click', function () {
                navItems.forEach(navItem => {
                    navItem.querySelector('svg').classList.remove('text-blue-600');
                    navItem.querySelector('svg').classList.add('text-muted-foreground');

                    const span = navItem.querySelector('span');
                    span.classList.remove('text-blue-600', 'font-medium');
                    span.classList.add('text-muted-foreground');
                });

                this.classList.add('nav-pressed');
                setTimeout(() => {
                    this.classList.remove('nav-pressed');
                    markAsActive(this);
                }, 150);
            });
        });

        function markAsActive(element) {
            const icon = element.querySelector('svg');
            icon.classList.remove('text-muted-foreground');
            icon.classList.add('text-blue-600');

            const label = element.querySelector('span');
            label.classList.remove('text-muted-foreground');
            label.classList.add('text-blue-600', 'font-medium');
        }

        // Mobile filters functionality
        const filtersBtn = document.getElementById('mobile-filters-btn');
        const filtersOverlay = document.getElementById('mobile-filters-overlay');
        const closeFilters = document.getElementById('close-filters');

        if (filtersBtn) {
            filtersBtn.addEventListener('click', function(e) {
                e.preventDefault();
                filtersOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        }

        if (closeFilters) {
            closeFilters.addEventListener('click', function() {
                filtersOverlay.classList.add('hidden');
                document.body.style.overflow = 'auto';
            });
        }

        if (filtersOverlay) {
            filtersOverlay.addEventListener('click', function(e) {
                if (e.target === filtersOverlay) {
                    filtersOverlay.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            });
        }
    });
</script>

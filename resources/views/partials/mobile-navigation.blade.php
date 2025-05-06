<!-- Composant mobile-nav -->
<div class="md:hidden fixed bottom-0 left-0 right-0 border-t border-gray-200 bg-white z-50" id="mobile-nav">
    <div class="flex items-center justify-around h-16">
        <!-- Accueil -->
        <a href="/" class="nav-item flex flex-col items-center justify-center w-full h-full" data-page="home">
            <svg class="h-5 w-5 icon-home" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
            </svg>
            <span class="text-xs mt-1 label-home">Home</span>
        </a>

        <!-- Création -->
        <a href="/dashboard/campaigns/new" class="nav-item flex flex-col items-center justify-center w-full h-full" data-page="create">
            <div class="bg-blue-600 text-white p-2 rounded-full">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="M12 5v14" />
                </svg>
            </div>
            <span class="text-xs mt-1 text-muted-foreground">Create</span>
        </a>

        <!-- Connexion -->
        <a href="/login" class="nav-item flex flex-col items-center justify-center w-full h-full" data-page="login">
            <svg class="h-5 w-5 icon-login" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
            </svg>
            <span class="text-xs mt-1 label-login">Login</span>
        </a>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const currentPath = window.location.pathname;
        let activePage = 'home';

        if (currentPath === '/') activePage = 'home';
        else if (currentPath.includes('/create')) activePage = 'create';
        else if (currentPath.includes('/login')) activePage = 'login';

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
    });

</script>

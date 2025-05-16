<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Digipost</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col md:flex-row">
<!-- Mobile Header -->
<div class="md:hidden flex items-center justify-between p-4 border-b">
    <div class="flex items-center gap-2">
        <div class="h-8 w-8 rounded-md bg-primary"></div>
        <span class="text-xl font-bold">Digipost</span>
    </div>
    <button type="button" class="p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100" onclick="toggleSidebar()">
        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" x2="20" y1="12" y2="12"/>
            <line x1="4" x2="20" y1="6" y2="6"/>
            <line x1="4" x2="20" y1="18" y2="18"/>
        </svg>
    </button>
</div>

<!-- Sidebar for desktop -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-background border-r transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 -translate-x-full">
    <div class="flex flex-col h-full">
        <div class="p-4 border-b">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-md bg-primary"></div>
                <span class="text-xl font-bold">Digipost</span>
            </div>
        </div>

        <div class="flex-1 py-6 px-4 space-y-1 overflow-y-auto">
            @php
                $navItems = [
                    [
                        'title' => 'Dashboard',
                        'href' => '/dashboard',
                        'icon' => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 22V12h6v10',
                    ],
                    [
                        'title' => 'My Campaigns',
                        'href' => '/dashboard/campaigns',
                        'icon' => 'M8 2v4 M16 2v4 M3.5 10h17 M21 10v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8',
                    ],
                    [
                        'title' => 'Registrations',
                        'href' => '/dashboard/registrations',
                        'icon' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M22 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75',
                    ],
                    [
                        'title' => 'Create Campaign',
                        'href' => '/dashboard/campaigns/new',
                        'icon' => 'M12 5v14 M5 12h14 M12 5v14 M5 12h14',
                    ],
                    [
                        'title' => 'Settings',
                        'href' => '/dashboard/settings',
                        'icon' => 'M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z M9 12a3 3 0 1 0 6 0 3 3 0 0 0-6 0',
                    ],
                ];
            @endphp

            @foreach($navItems as $item)
                <a href="{{ $item['href'] }}" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is(ltrim($item['href'], '/')) ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['title'] }}
                </a>
            @endforeach
        </div>

        <div class="p-4 border-t">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-3 py-2 text-sm font-medium text-muted-foreground hover:bg-muted rounded-md">
                    <svg class="mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" x2="9" y1="12" y2="12"/>
                    </svg>
                    Log out
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Main content -->
<div class="flex-1 md:ml-64 flex flex-col">
    <main class="flex-1 p-4 md:p-8">
        @yield('content')
    </main>
</div>

<!-- Mobile Navigation -->
{{--@include('components.mobile-navigation')--}}

<!-- Backdrop for mobile sidebar -->
<div id="sidebarBackdrop" class="hidden fixed inset-0 bg-black/50 z-40 md:hidden" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            backdrop.classList.remove('hidden');
        } else {
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }
    }
</script>
</body>
</html>

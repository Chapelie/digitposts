<div class="md:hidden fixed bottom-0 left-0 right-0 border-t bg-background z-50">
    <div class="flex items-center justify-around h-16">
        <a href="/" class="flex flex-col items-center justify-center w-full h-full">
            <svg class="h-5 w-5 {{ request()->is('/') ? 'text-primary' : 'text-muted-foreground' }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <span class="text-xs mt-1 {{ request()->is('/') ? 'text-primary font-medium' : 'text-muted-foreground' }}">
                Home
            </span>
        </a>

        <a href="/search" class="flex flex-col items-center justify-center w-full h-full">
            <svg class="h-5 w-5 {{ request()->is('search') ? 'text-primary' : 'text-muted-foreground' }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.3-4.3"/>
            </svg>
            <span class="text-xs mt-1 {{ request()->is('search') ? 'text-primary font-medium' : 'text-muted-foreground' }}">
                Search
            </span>
        </a>

        <a href="/create" class="flex flex-col items-center justify-center w-full h-full">
            <div class="bg-primary text-primary-foreground p-2 rounded-full">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>
            </div>
            <span class="text-xs mt-1 text-muted-foreground">Create</span>
        </a>

        <a href="/login" class="flex flex-col items-center justify-center w-full h-full">
            <svg class="h-5 w-5 {{ request()->is('login') ? 'text-primary' : 'text-muted-foreground' }}" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <span class="text-xs mt-1 {{ request()->is('login') ? 'text-primary font-medium' : 'text-muted-foreground' }}">
                Login
            </span>
        </a>
    </div>
</div>

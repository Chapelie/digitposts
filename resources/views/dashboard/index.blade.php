@extends('layouts.dashboard')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                <p class="text-muted-foreground">Welcome back, Creator! Here's an overview of your campaigns.</p>
            </div>
            <a href="/dashboard/campaigns/new">
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"/>
                        <path d="M12 5v14"/>
                    </svg>
                    New Campaign
                </button>
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Total Campaigns Card -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Total Campaigns</h3>
                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 2v4"/>
                        <path d="M16 2v4"/>
                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                        <path d="M3 10h18"/>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">12</div>
                    <p class="text-xs text-muted-foreground">+2 from last month</p>
                </div>
            </div>

            <!-- Total Registrations Card -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Total Registrations</h3>
                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">245</div>
                    <p class="text-xs text-muted-foreground">+18% from last month</p>
                </div>
            </div>

            <!-- Upcoming Events Card -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Upcoming Events</h3>
                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 2v4"/>
                        <path d="M16 2v4"/>
                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                        <path d="M3 10h18"/>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">4</div>
                    <p class="text-xs text-muted-foreground">Next event in 3 days</p>
                </div>
            </div>

            <!-- Completion Rate Card -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="flex flex-row items-center justify-between space-y-0 p-6 pb-2">
                    <h3 class="text-sm font-medium">Completion Rate</h3>
                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                        <polyline points="16 7 22 7 22 13"/>
                    </svg>
                </div>
                <div class="p-6 pt-0">
                    <div class="text-2xl font-bold">92%</div>
                    <p class="text-xs text-muted-foreground">+5% from last month</p>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div x-data="{ activeTab: 'upcoming' }">
            <div class="inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground">
                <button @click="activeTab = 'upcoming'" :class="{'bg-background text-foreground shadow-sm': activeTab === 'upcoming'}" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                    Upcoming
                </button>
                <button @click="activeTab = 'active'" :class="{'bg-background text-foreground shadow-sm': activeTab === 'active'}" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                    Active
                </button>
                <button @click="activeTab = 'past'" :class="{'bg-background text-foreground shadow-sm': activeTab === 'past'}" class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50">
                    Past
                </button>
            </div>

            <!-- Upcoming Tab Content -->
            <div x-show="activeTab === 'upcoming'" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Campaign Card 1 -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold leading-none tracking-tight">Advanced Web Development</h3>
                            <p class="text-sm text-muted-foreground">Training • May 15-20, 2025</p>
                        </div>
                        <div class="p-6 pt-0">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span class="text-sm text-muted-foreground">24 registrations</span>
                                </div>
                                <a href="/dashboard/campaigns/1">
                                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 py-2">
                                        Manage
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Card 2 -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold leading-none tracking-tight">Digital Marketing Conference</h3>
                            <p class="text-sm text-muted-foreground">Event • June 5-7, 2025</p>
                        </div>
                        <div class="p-6 pt-0">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span class="text-sm text-muted-foreground">42 registrations</span>
                                </div>
                                <a href="/dashboard/campaigns/2">
                                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 py-2">
                                        Manage
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Card 3 -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold leading-none tracking-tight">UX Design Workshop</h3>
                            <p class="text-sm text-muted-foreground">Training • July 10, 2025</p>
                        </div>
                        <div class="p-6 pt-0">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span class="text-sm text-muted-foreground">18 registrations</span>
                                </div>
                                <a href="/dashboard/campaigns/3">
                                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 py-2">
                                        Manage
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Tab Content -->
            <div x-show="activeTab === 'active'" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Active Campaign Card -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold leading-none tracking-tight">Product Management Certification</h3>
                            <p class="text-sm text-muted-foreground">Training • Ongoing</p>
                        </div>
                        <div class="p-6 pt-0">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span class="text-sm text-muted-foreground">36 registrations</span>
                                </div>
                                <a href="/dashboard/campaigns/5">
                                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 py-2">
                                        Manage
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Past Tab Content -->
            <div x-show="activeTab === 'past'" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Past Campaign Card 1 -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold leading-none tracking-tight">JavaScript Fundamentals</h3>
                            <p class="text-sm text-muted-foreground">Training • Completed</p>
                        </div>
                        <div class="p-6 pt-0">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span class="text-sm text-muted-foreground">52 registrations</span>
                                </div>
                                <a href="/dashboard/campaigns/7">
                                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 py-2">
                                        View Report
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Past Campaign Card 2 -->
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold leading-none tracking-tight">Tech Startup Summit</h3>
                            <p class="text-sm text-muted-foreground">Event • Completed</p>
                        </div>
                        <div class="p-6 pt-0">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                    <span class="text-sm text-muted-foreground">73 registrations</span>
                                </div>
                                <a href="/dashboard/campaigns/8">
                                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3 py-2">
                                        View Report
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Recent Activity</h2>
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-3 py-2">
                    View all
                </button>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="p-0">
                    <div class="divide-y">
                        <!-- Activity Item 1 -->
                        <div class="flex items-center gap-4 p-4">
                            <div class="bg-primary/10 p-2 rounded-full">
                                <svg class="h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">New registration for Advanced Web Development</p>
                                <p class="text-xs text-muted-foreground">John Doe registered 2 hours ago</p>
                            </div>
                        </div>

                        <!-- Activity Item 2 -->
                        <div class="flex items-center gap-4 p-4">
                            <div class="bg-primary/10 p-2 rounded-full">
                                <svg class="h-4 w-4 text-primary" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 2v4"/>
                                    <path d="M16 2v4"/>
                                    <rect width="18" height="18" x="3" y="4" rx="2"/>
                                    <path d="M3 10h18"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Campaign published</p>
                                <p class="text-xs text-muted-foreground">UX Design Workshop was published 1 day ago</p>
                            </div>
                        </div>

                        <!-- Activity Item 3 -->
                        <div class="flex items-center gap-4 p-4">
                            <div class="bg-yellow-500/10 p-2 rounded-full">
                                <svg class="h-4 w-4 text-yellow-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" x2="12" y1="8" y2="12"/>
                                    <line x1="12" x2="12.01" y1="16" y2="16"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">Campaign deadline approaching</p>
                                <p class="text-xs text-muted-foreground">
                                    Digital Marketing Conference registration closes in 3 days
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js for tab functionality -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection

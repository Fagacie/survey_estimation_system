<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ISES') }} — Eco Hydrotech</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 CSS (kept for legacy views) -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Scripts (Tailwind & JS) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- FontAwesome (Using lighter weight approach) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Leaflet & Draw JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
        <!-- Turf.js -->
        <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() + 1 }}">
    </head>
    
    <body class="bg-slate-50 font-sans antialiased text-slate-800" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
        
        <div class="flex h-screen overflow-hidden">
            
            <!-- OVERLAY (Mobile) -->
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-slate-900/50 lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

            <!-- 1. SIDE NAVIGATION -->
            @unless($hideSidebar ?? false)
                <aside :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0 lg:w-[4.5rem]'" class="fixed inset-y-0 left-0 z-30 bg-slate-900 text-slate-300 transition-all duration-300 ease-in-out flex flex-col shadow-2xl lg:shadow-none border-r border-slate-800 lg:static relative group">
                    
                    <!-- Sidebar Header / Logo -->
                    <div class="flex items-center h-16 border-b border-slate-800 flex-shrink-0" :class="sidebarOpen ? 'px-4 justify-between' : 'justify-center'">
                        <a href="{{ url('/') }}" class="flex items-center text-white hover:text-teal-400 transition-colors overflow-hidden whitespace-nowrap" title="Dashboard">
                            <x-application-logo class="h-6 w-auto text-teal-500 flex-shrink-0" style="color: #14b8a6;" />
                        </a>
                        
                        <!-- Toggle Button Integrated into Sidebar Header -->
                        <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex items-center justify-center w-6 h-6 text-slate-400 hover:text-white transition-colors focus:outline-none" title="Toggle Sidebar">
                            <i class="fa-solid text-xs" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                        </button>
                    </div>

                    <!-- Sidebar Navigation Links -->
                    <div class="flex-1 overflow-y-auto py-6 flex flex-col gap-1.5 custom-scrollbar" :class="sidebarOpen ? 'px-3' : 'px-2 items-center'">
                        
                        <a href="{{ route('projects.index') }}" title="Projects" class="flex items-center px-3 py-2.5 transition-all group {{ request()->is('projects') || request()->is('projects/create') ? 'bg-teal-500/10 text-white border-l-2 border-teal-500' : 'hover:bg-slate-800 hover:text-white text-slate-400 border-l-2 border-transparent' }}" :class="sidebarOpen ? 'gap-3 w-full' : 'justify-center w-full'">
                            <i class="fa-solid fa-layer-group w-5 text-center text-sm transition-colors {{ request()->is('projects') || request()->is('projects/create') ? 'text-teal-400' : 'group-hover:text-slate-300' }}"></i> 
                            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Projects</span>
                        </a>
                        <a href="{{ route('clients.index') }}" title="Clients" class="flex items-center px-3 py-2.5 transition-all group {{ request()->is('clients*') ? 'bg-teal-500/10 text-white border-l-2 border-teal-500' : 'hover:bg-slate-800 hover:text-white text-slate-400 border-l-2 border-transparent' }}" :class="sidebarOpen ? 'gap-3 w-full' : 'justify-center w-full'">
                            <i class="fa-solid fa-users w-5 text-center text-sm transition-colors {{ request()->is('clients*') ? 'text-teal-400' : 'group-hover:text-slate-300' }}"></i> 
                            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Clients</span>
                        </a>
                        <a href="{{ route('settings.costs') }}" title="Settings" class="flex items-center px-3 py-2.5 transition-all group {{ request()->is('settings*') ? 'bg-teal-500/10 text-white border-l-2 border-teal-500' : 'hover:bg-slate-800 hover:text-white text-slate-400 border-l-2 border-transparent' }}" :class="sidebarOpen ? 'gap-3 w-full' : 'justify-center w-full'">
                            <i class="fa-solid fa-gear w-5 text-center text-sm transition-colors {{ request()->is('settings*') ? 'text-teal-400' : 'group-hover:text-slate-300' }}"></i> 
                            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Settings</span>
                        </a>

                        <!-- Sub-navigations removed to keep sidebar minimal as requested -->

                    </div>

                    <!-- Sidebar Footer -->
                    <div class="border-t border-slate-800 flex-shrink-0 flex flex-col">
                        
                        <!-- User Info -->
                        @auth
                            <div class="p-3 bg-slate-950 flex items-center justify-center transition-all border-t border-slate-800" :class="sidebarOpen ? '' : 'flex-col gap-2'">
                                <a href="{{ route('profile.edit') }}" title="Profile" class="w-8 h-8 rounded bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700 flex items-center justify-center font-semibold text-xs flex-shrink-0 transition-colors">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </a>
                                <div x-show="sidebarOpen" class="flex-1 min-w-0 ml-3">
                                    <div class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</div>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" title="Logout" :class="sidebarOpen ? 'ml-2' : ''">
                                    @csrf
                                    <button type="submit" class="text-slate-500 hover:text-white transition-colors p-1.5 rounded hover:bg-slate-800 focus:outline-none flex items-center justify-center w-8 h-8">
                                        <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        @endauth
                    </div>
                </aside>
            @endunless

            <!-- 2. MAIN CONTENT AREA -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative bg-white">
                
                <!-- TOP UTILITY HEADER -->
                <!-- Removed branding, removed hamburger (handled in sidebar), clean white BG, 1px bottom border -->
                <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 z-10 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        @unless($hideSidebar ?? false)
                            <!-- Hamburger only visible on mobile -->
                            <button @click="sidebarOpen = true" class="text-slate-500 hover:text-slate-800 focus:outline-none p-1.5 rounded hover:bg-slate-100 transition-colors lg:hidden mr-2">
                                <i class="fa-solid fa-bars"></i>
                            </button>
                        @else
                            <!-- If sidebar is completely hidden (e.g. Map View) -->
                            @if(request()->route('project'))
                                @php
                                    $project = request()->route('project');
                                    $projectId = is_object($project) ? $project->id : $project;
                                @endphp
                                <a href="{{ route('projects.show', $projectId) }}" class="flex items-center gap-2 text-slate-600 hover:text-slate-900 transition-colors font-medium text-sm">
                                    <i class="fa-solid fa-arrow-left text-xs"></i> Back to Project
                                </a>
                            @else
                                <a href="{{ route('projects.index') }}" class="flex items-center gap-2 text-slate-600 hover:text-slate-900 transition-colors font-medium text-sm">
                                    <i class="fa-solid fa-arrow-left text-xs"></i> Dashboard
                                </a>
                            @endif
                            <div class="h-4 w-px bg-slate-300 mx-3"></div>
                            <span class="text-sm font-medium text-slate-800 tracking-wide">Map Engine</span>
                        @endunless
                        
                        <!-- Page Title / Breadcrumb (Dynamic) -->
                        @if(isset($header))
                            <div class="text-sm font-medium text-slate-600">
                                {{ $header }}
                            </div>
                        @endif
                    </div>
                </header>

                <!-- PAGE CONTENT -->
                <!-- Removed max-w, using w-full px-8 for full fluid layout -->
                <main class="flex-1 overflow-y-auto overflow-x-hidden bg-slate-50 {{ $containerClass ?? 'w-full' }}">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <style>
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #334155; border-radius: 20px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #475569; }
        </style>
    </body>
</html>

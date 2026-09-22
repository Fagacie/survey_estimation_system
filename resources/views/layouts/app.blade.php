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
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

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

        <!-- Custom CSS -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() + 1 }}">
        @stack('styles')
    </head>
    
    <body class="ises-app bg-slate-50 antialiased text-slate-800" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
        
        <div class="flex h-screen overflow-hidden">
            
            <!-- OVERLAY (Mobile) -->
            <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-20 bg-slate-900/50 lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

            <!-- 1. SIDE NAVIGATION -->
            @unless($hideSidebar ?? false)
                <aside :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0 lg:w-[4.5rem]'" class="ises-sidebar fixed inset-y-0 left-0 z-30 transition-all duration-300 ease-in-out flex flex-col lg:static relative group">
                    
                    <!-- Sidebar Header / Logo -->
                    <div class="ises-sidebar-brand flex items-center h-16 flex-shrink-0" :class="sidebarOpen ? 'px-4 justify-between' : 'justify-center'">
                        <a href="{{ route('projects.index') }}" class="flex items-center gap-3 ises-brand overflow-hidden whitespace-nowrap" title="Survey Projects">
                            <span class="ises-brand-mark"><i class="fa-solid fa-compass-drafting"></i></span>
                            <span x-show="sidebarOpen" class="ises-brand-copy"><strong>ISES</strong><small>Survey Operations</small></span>
                        </a>
                        
                        <!-- Toggle Button Integrated into Sidebar Header -->
                        <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:flex items-center justify-center w-6 h-6 text-slate-400 hover:text-white transition-colors focus:outline-none" title="Toggle Sidebar">
                            <i class="fa-solid text-xs" :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"></i>
                        </button>
                    </div>

                    <!-- Sidebar Navigation Links -->
                    <div class="flex-1 overflow-y-auto py-6 flex flex-col gap-1.5 custom-scrollbar" :class="sidebarOpen ? 'px-3' : 'px-2 items-center'">
                        
                        <a href="{{ route('projects.index') }}" title="Survey Projects" class="ises-nav-link flex items-center px-3 py-2.5 transition-all group" :class="sidebarOpen ? 'gap-3 w-full' : 'justify-center w-full'">
                            <i class="fa-solid fa-map-location-dot w-5 text-center text-sm"></i>
                            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Survey Projects</span>
                        </a>
                        <a href="{{ route('quotations.history') }}" title="Quotation History" class="ises-nav-link flex items-center px-3 py-2.5 transition-all group" :class="sidebarOpen ? 'gap-3 w-full' : 'justify-center w-full'">
                            <i class="fa-solid fa-file-invoice-dollar w-5 text-center text-sm"></i>
                            <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Quotation History</span>
                        </a>


                        <!-- Items Management Navigation Dropdown -->
                        <div x-data="{ itemsOpen: false }" class="w-full">
                            <button @click="itemsOpen = !itemsOpen" title="Items Database" class="ises-nav-link flex items-center justify-between px-3 py-2.5 transition-all group w-full focus:outline-none" :class="sidebarOpen ? '' : 'justify-center'">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-database w-5 text-center text-sm"></i>
                                    <span x-show="sidebarOpen" class="text-sm font-medium whitespace-nowrap">Items Database</span>
                                </div>
                                <i x-show="sidebarOpen" class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" :class="itemsOpen ? 'rotate-180' : ''"></i>
                            </button>
                            
                            <!-- Sub-navigation -->
                            <div x-show="itemsOpen && sidebarOpen" x-transition class="pl-11 pr-3 py-1 space-y-1">
                                <a href="{{ route('admin.index') }}" class="block px-3 py-2 text-xs font-medium text-slate-500 rounded-lg hover:bg-slate-100 hover:text-slate-800 transition-colors {{ request()->routeIs('admin.index') ? 'bg-slate-100 text-teal-600' : '' }}">
                                    View All Items
                                </a>
                                <a href="{{ route('newItem') }}" class="block px-3 py-2 text-xs font-medium text-slate-500 rounded-lg hover:bg-slate-100 hover:text-slate-800 transition-colors {{ request()->routeIs('newItem') ? 'bg-slate-100 text-teal-600' : '' }}">
                                    + Add New Item
                                </a>
                            </div>
                        </div>

                        <!-- Sub-navigations removed to keep sidebar minimal as requested -->                    </div>

                    <!-- Sidebar Footer -->
                    <div class="border-t border-slate-800 flex-shrink-0 flex flex-col">
                        
                        <!-- User Info -->
                        @auth
                            <div class="ises-sidebar-user p-3 flex items-center justify-center transition-all" :class="sidebarOpen ? '' : 'flex-col gap-2'">
                                <a href="{{ route('profile.edit') }}" title="Profile" class="ises-avatar flex items-center justify-center font-semibold text-xs flex-shrink-0 transition-colors">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </a>
                                <div x-show="sidebarOpen" class="flex-1 min-w-0 ml-3">
                                    <div class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</div>
                                </div>
                                <form method="POST" action="{{ route('logout') }}" title="Logout" :class="sidebarOpen ? 'ml-2' : ''">
                                    @csrf
                                    <button type="submit" class="text-slate-400 hover:text-rose-600 transition-colors p-1.5 rounded hover:bg-rose-50 focus:outline-none flex items-center justify-center w-8 h-8">
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
                <header class="ises-topbar h-16 flex items-center justify-between px-8 z-10 flex-shrink-0">
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
                                    $projectId = is_object($project) ? $project->project_Id : $project;
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
        
        @stack('scripts')
    </body>
</html>

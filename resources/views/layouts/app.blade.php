<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Eco Hydrotech Solutions</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        @vite(['resources/css/ises.css', 'resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" onerror="this.onerror=null;this.href='https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css'" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" onerror="this.onerror=null;this.href='https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css'" />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" onerror="this.onerror=null;this.src='https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js'"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js" onerror="this.onerror=null;this.src='https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js'"></script>
        <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
        <link rel="stylesheet" href="{{ asset('css/ises-redesign.css') }}?v={{ filemtime(public_path('css/ises-redesign.css')) }}">
        <link rel="stylesheet" href="{{ asset('css/ises-page-overrides.css') }}?v={{ filemtime(public_path('css/ises-page-overrides.css')) }}">
        @stack('styles')
        <link rel="stylesheet" href="{{ asset('css/ises-final-overrides.css') }}?v={{ filemtime(public_path('css/ises-final-overrides.css')) }}">
        <style>
            @media print { .ises-sidebar,.ises-topbar,.no-print { display:none!important; } html,body,.ises-app,.ises-shell,.ises-main,.ises-content,main { display:block!important;height:auto!important;min-height:auto!important;overflow:visible!important;background:#fff!important;color:#000!important;print-color-adjust:exact!important;-webkit-print-color-adjust:exact!important; } .page-container,.invoice-slip,.quote-page,.invoice-page-wrap { box-shadow:none!important;border:0!important;margin:0!important;padding:0!important;width:100%!important;max-width:100%!important; } * { box-shadow:none!important; } }
        </style>
    </head>
    <body class="ises-app bg-slate-50 antialiased text-slate-800">
        <div class="ises-shell {{ ($hideSidebar ?? false) ? 'ises-shell--no-sidebar' : '' }} print:block">
            @unless($hideSidebar ?? false)
                <div id="isesMobileOverlay" class="ises-mobile-overlay" aria-hidden="true"></div>
                <aside id="isesSidebar" class="ises-sidebar print:hidden" aria-label="Main navigation">
                    <div class="ises-sidebar-header">
                        <a href="{{ route('dashboard') }}" class="ises-brand" aria-label="Eco HydroTech home">
                            <div class="ises-brand-mark">
                                <img src="{{ asset('images/logo.png') }}" alt="Eco HydroTech Logo" class="ises-brand-logo-img">
                            </div>
                            <div class="ises-brand-text">
                                <span class="ises-brand-name ises-brand-title">Eco HydroTech</span>
                                <span class="ises-brand-caption ises-brand-subtitle">Survey Estimation System</span>
                            </div>
                        </a>
                        <button type="button" id="isesMobileCloseBtn" class="ises-mobile-close-btn" aria-label="Close navigation drawer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <nav class="ises-nav" aria-label="Workspace pages">
                        <a href="{{ route('dashboard') }}" class="ises-nav-link {{ request()->routeIs('dashboard', 'home') ? 'is-active' : '' }}" {{ request()->routeIs('dashboard', 'home') ? 'aria-current=page' : '' }}><span class="ises-nav-icon"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>Overview</a>
                        <a href="{{ route('projects.index') }}" class="ises-nav-link {{ request()->routeIs('projects.index', 'projects.create', 'projects.show', 'projects.edit') ? 'is-active' : '' }}" {{ request()->routeIs('projects.index', 'projects.create', 'projects.show', 'projects.edit') ? 'aria-current=page' : '' }}><span class="ises-nav-icon"><i class="fa-solid fa-map-location-dot" aria-hidden="true"></i></span>Projects</a>
                        <a href="{{ route('quotations.history') }}" class="ises-nav-link {{ request()->routeIs('quotations.*', 'quotation.*') ? 'is-active' : '' }}" {{ request()->routeIs('quotations.*', 'quotation.*') ? 'aria-current=page' : '' }}><span class="ises-nav-icon"><i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i></span>Quotations</a>
                        <a href="{{ route('admin.index') }}" class="ises-nav-link {{ request()->routeIs('admin.index', 'newItem', 'items.*') ? 'is-active' : '' }}" {{ request()->routeIs('admin.index', 'newItem', 'items.*') ? 'aria-current=page' : '' }}><span class="ises-nav-icon"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>Admin catalogue</a>
                        <a href="{{ route('admin.equipment.index') }}" class="ises-nav-link {{ request()->routeIs('admin.equipment.*') ? 'is-active' : '' }}" {{ request()->routeIs('admin.equipment.*') ? 'aria-current=page' : '' }}><span class="ises-nav-icon"><i class="fa-solid fa-camera" aria-hidden="true"></i></span>Drone equipment</a>
                    </nav>
                    <div class="ises-sidebar-account" aria-label="Account actions">
                        @auth
                            <a class="ises-sidebar-user-card" href="{{ route('profile.edit') }}" aria-label="Profile settings" title="Profile settings">
                                <span class="ises-avatar" aria-label="Signed-in user">{{ collect(preg_split('/\s+/', auth()->user()->name))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('') }}</span>
                                <div class="ises-sidebar-user-info">
                                    <span class="ises-sidebar-user-name">{{ auth()->user()->name }}</span>
                                    <span class="ises-sidebar-user-role">Profile settings</span>
                                </div>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="ises-sidebar-logout-form">
                                @csrf
                                <button type="submit" class="ises-sidebar-logout-btn" aria-label="Log out" title="Log out">
                                    <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                                </button>
                            </form>
                        @endauth
                    </div>
                </aside>
            @endunless
            <div class="ises-main">
                <header class="ises-topbar print:hidden">
                    <div class="ises-topbar-left">
                        @unless($hideSidebar ?? false)
                            <button type="button" id="isesMobileNavToggle" class="ises-mobile-nav-toggle" aria-label="Toggle navigation menu" aria-expanded="false">
                                <i class="fa-solid fa-bars" id="isesMobileNavToggleIcon"></i>
                            </button>
                        @endunless
                        <div class="ises-breadcrumb">
                            @if($hideSidebar ?? false)
                                @if(request()->route('project'))
                                    @php $project = request()->route('project'); $projectId = is_object($project) ? $project->project_Id : $project; @endphp
                                    <a href="{{ route('projects.show', $projectId) }}" class="map-back-project"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to Project</a>
                                @else
                                    <a href="{{ route('dashboard') }}" class="map-back-project"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Dashboard</a>
                                @endif
                                <span aria-hidden="true"> / </span><strong>Survey Planning</strong>
                            @else
                                <span>Workspace</span><span aria-hidden="true"> / </span><strong>{{ $header ?? 'Workspace' }}</strong>
                            @endif
                        </div>
                    </div>
                    <div class="ises-topbar-right">
                        <span class="ises-topbar-date">{{ now()->format('d M Y') }}</span>
                        @auth
                            <span class="ises-avatar" aria-label="Signed-in user">{{ collect(preg_split('/\s+/', auth()->user()->name))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('') }}</span>
                            <a class="ises-panel-action" href="{{ route('profile.edit') }}">{{ auth()->user()->name }}</a>
                        @endauth
                    </div>
                </header>
                <main class="ises-content {{ $containerClass ?? 'w-full' }} print:overflow-visible print:bg-white">{{ $slot }}</main>
            </div>
        </div>
        <style>
            @media print { .ises-sidebar,.ises-topbar,.no-print { display:none!important; } html,body,.ises-app,.ises-shell,.ises-main,.ises-content,main { display:block!important;height:auto!important;min-height:auto!important;overflow:visible!important;background:#fff!important;color:#000!important;print-color-adjust:exact!important;-webkit-print-color-adjust:exact!important; } .page-container,.invoice-slip,.quote-page,.invoice-page-wrap { box-shadow:none!important;border:0!important;margin:0!important;padding:0!important;width:100%!important;max-width:100%!important; } * { box-shadow:none!important; } }
        </style>
        <script>
            (function() {
                var toggle = document.getElementById('isesMobileNavToggle');
                var closeBtn = document.getElementById('isesMobileCloseBtn');
                var overlay = document.getElementById('isesMobileOverlay');
                var sidebar = document.getElementById('isesSidebar');
                if (!sidebar) return;

                function openNav() {
                    sidebar.classList.add('is-open');
                    if (overlay) overlay.classList.add('is-visible');
                    if (toggle) toggle.setAttribute('aria-expanded', 'true');
                }

                function closeNav() {
                    sidebar.classList.remove('is-open');
                    if (overlay) overlay.classList.remove('is-visible');
                    if (toggle) toggle.setAttribute('aria-expanded', 'false');
                }

                if (toggle) {
                    toggle.addEventListener('click', function() {
                        if (sidebar.classList.contains('is-open')) closeNav();
                        else openNav();
                    });
                }
                if (closeBtn) closeBtn.addEventListener('click', closeNav);
                if (overlay) overlay.addEventListener('click', closeNav);
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && sidebar.classList.contains('is-open')) closeNav();
                });
            })();
        </script>
        @stack('scripts')
    </body>
</html>

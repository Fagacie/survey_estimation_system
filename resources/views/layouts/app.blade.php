<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ISES') }} — INOS Survey Estimation System</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- FontAwesome -->
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

        <style>
            /* ============================================================
               DYNAMIC ISLAND NAVBAR
               ============================================================ */
            body {
                padding-top: 70px; /* reserve space for fixed navbar */
            }
            /* On the full-screen workspace page, remove the body padding
               so the workspace fills edge-to-edge */
            body.workspace-page {
                padding-top: 0;
            }

            .ises-navbar {
                position: fixed;
                top: 10px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 9999;
                width: calc(100% - 48px);
                max-width: 900px;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid rgba(0, 0, 0, 0.05);
                border-radius: 40px;
                padding: 10px 16px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(0,0,0,0.02);
                transition: all 0.3s ease;
            }
            .ises-navbar:hover {
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0,0,0,0.03);
            }
            .ises-navbar .navbar-brand {
                color: #0f172a;
                font-weight: 800;
                font-size: 1.1rem;
                letter-spacing: 0.5px;
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .ises-navbar .navbar-brand .brand-icon {
                font-weight: 900;
                color: #0f172a;
                background: #0f172a;
                color: #ffffff;
                font-size: 1.2rem;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .ises-navbar .nav-link {
                color: #64748b !important;
                font-size: 0.85rem;
                font-weight: 600;
                padding: 6px 16px !important;
                border-radius: 20px;
                transition: all 0.2s;
                text-decoration: none;
            }
            .ises-navbar .nav-link:hover {
                color: #0f172a !important;
                background: rgba(0, 0, 0, 0.04);
            }
            .ises-navbar .nav-link.active {
                color: #0f172a !important;
                background: rgba(0, 0, 0, 0.04);
            }
            .ises-navbar .nav-divider {
                width: 1px;
                height: 20px;
                background: rgba(0, 0, 0, 0.08);
                margin: 0 8px;
            }
            .btn-island {
                background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%);
                color: #ffffff;
                font-size: 0.85rem;
                font-weight: 700;
                border: none;
                border-radius: 24px;
                padding: 8px 24px;
                transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
            }
            .btn-island:hover {
                color: #ffffff;
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(2, 132, 199, 0.4);
            }

            /* Page header below the navbar (non-workspace pages) */
            .page-header-bar {
                background: #fff;
                border-bottom: 1px solid #eef2f5;
                padding: 14px 0;
                margin-bottom: 24px;
            }
            .page-header-bar h4 {
                font-size: 1rem;
                font-weight: 700;
                color: #0f172a;
                margin: 0;
            }
        </style>
    </head>
    <body class="{{ isset($containerClass) && str_contains((string) ($containerClass ?? ''), 'px-0') ? 'workspace-page' : '' }} bg-light">

        <!-- DYNAMIC ISLAND NAVBAR -->
        <nav class="ises-navbar d-flex align-items-center justify-content-between">
            <!-- Brand -->
            <a class="navbar-brand" href="{{ url('/') }}">
                <div class="brand-icon">Λ</div>
                <div>
                    ISES
                </div>
            </a>

            <!-- Central Links -->
            <div class="d-none d-md-flex align-items-center gap-2">
                <a class="nav-link {{ request()->is('projects*') ? 'active' : '' }}" href="{{ route('projects.index') }}">Projects</a>
                <a class="nav-link" href="#">Clients</a>
                <a class="nav-link {{ request()->is('settings*') ? 'active' : '' }}" href="{{ route('settings.costs') }}">Settings</a>
            </div>

            <!-- Action Button / User -->
            <div class="d-flex align-items-center gap-2">
                @auth
                    @if(request()->routeIs('projects.map') || request()->routeIs('projects.show'))
                        <button class="btn-island ms-3 btn-save-planning-trigger">Save Plan</button>
                    @else
                        <a href="#" class="btn-island ms-3">Resume</a>
                    @endif
                @endauth
            </div>
        </nav>



        <!-- Page Content -->
        <main class="{{ $containerClass ?? 'container mb-5' }}">
            {{ $slot }}
        </main>

    </body>
</html>

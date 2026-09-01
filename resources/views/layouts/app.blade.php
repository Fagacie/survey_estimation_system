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
                top: 0;
                left: 0;
                z-index: 9999;
                width: 100%;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(226, 232, 240, 0.8);
                padding: 0;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                height: 64px;
            }
            .ises-navbar .container-fluid {
                height: 100%;
                padding: 0 1.5rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                max-width: 1400px;
                margin: 0 auto;
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
                padding: 0 16px !important;
                transition: all 0.2s;
                text-decoration: none;
                height: 100%;
                display: flex;
                align-items: center;
                border-bottom: 2px solid transparent;
            }
            .ises-navbar .nav-link:hover {
                color: #0f172a !important;
                border-bottom-color: #cbd5e1;
            }
            .ises-navbar .nav-link.active {
                color: #0f172a !important;
                border-bottom-color: #3b82f6;
            }
            .ises-navbar .nav-divider {
                width: 1px;
                height: 20px;
                background: rgba(0, 0, 0, 0.08);
                margin: 0 8px;
            }
            .btn-island {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                color: #ffffff !important;
                font-size: 0.85rem;
                font-weight: 600;
                border: none;
                border-radius: 99px;
                padding: 8px 20px;
                transition: all 0.2s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);
            }
            .btn-island:hover {
                transform: translateY(-1px);
                box-shadow: 0 6px 16px rgba(59, 130, 246, 0.35);
            }
            .user-avatar {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: #e2e8f0;
                color: #475569;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.8rem;
                font-weight: 700;
                margin-left: 12px;
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

        <!-- PREMIUM SAAS NAVBAR -->
        <nav class="ises-navbar">
            <div class="container-fluid">
                <!-- Brand / Logo -->
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}" style="height: 100%; padding: 0;">
                    <x-application-logo style="height: 45px; width: auto; color: #0f172a;" />
                </a>

                <!-- Central Links -->
                <div class="d-none d-md-flex align-items-center gap-2 h-100">
                    @if(request()->route('project') && !request()->routeIs('projects.index'))
                        @php
                            $project = request()->route('project');
                            $projectId = is_object($project) ? $project->id : $project;
                        @endphp
                        <a class="nav-link" href="{{ route('projects.index') }}">
                            <i class="fa-solid fa-arrow-left me-2 opacity-75"></i> All Projects
                        </a>
                        <div style="width: 1px; height: 24px; background: #e2e8f0; margin: 0 8px;"></div>
                        <a class="nav-link {{ request()->routeIs('projects.show') ? 'active' : '' }}" href="{{ route('projects.show', $projectId) }}">
                            <i class="fa-solid fa-chart-pie me-2 opacity-75"></i> Overview
                        </a>
                        <a class="nav-link {{ request()->routeIs('projects.surveys.map') ? 'active' : '' }}" href="{{ route('projects.show', $projectId) }}#map">
                            <i class="fa-solid fa-map-location-dot me-2 opacity-75"></i> Map
                        </a>
                        <a class="nav-link {{ request()->routeIs('projects.cost.show') ? 'active' : '' }}" href="{{ route('projects.cost.show', $projectId) }}">
                            <i class="fa-solid fa-calculator me-2 opacity-75"></i> Costing
                        </a>
                        <a class="nav-link {{ request()->routeIs('projects.report.*') ? 'active' : '' }}" href="{{ route('projects.report.preview', $projectId) }}">
                            <i class="fa-regular fa-file-pdf me-2 opacity-75"></i> Report
                        </a>
                    @else
                        <a class="nav-link {{ request()->is('projects*') ? 'active' : '' }}" href="{{ route('projects.index') }}">
                            <i class="fa-solid fa-layer-group me-2 opacity-75"></i> Projects
                        </a>
                        <a class="nav-link {{ request()->is('clients*') ? 'active' : '' }}" href="{{ route('clients.index') }}">
                            <i class="fa-solid fa-users me-2 opacity-75"></i> Clients
                        </a>
                        <a class="nav-link {{ request()->is('settings*') ? 'active' : '' }}" href="{{ route('settings.costs') }}">
                            <i class="fa-solid fa-gear me-2 opacity-75"></i> Settings
                        </a>
                    @endif
                </div>

                <!-- Action Button / User -->
                <div class="d-flex align-items-center">
                    @auth
                        <div class="dropdown">
                            <button class="btn d-flex align-items-center gap-2 p-1" style="border: none; background: transparent; transition: opacity 0.2s;" type="button" data-bs-toggle="dropdown" aria-expanded="false" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                <div style="text-align: right; line-height: 1.2;" class="d-none d-sm-block">
                                    <div style="font-size: 0.85rem; font-weight: 600; color: #0f172a;">{{ auth()->user()->name }}</div>
                                    <div style="font-size: 0.7rem; color: #64748b;">Administrator</div>
                                </div>
                                <div class="user-avatar" style="box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border: 1px solid #e2e8f0; border-radius: 12px; margin-top: 12px; min-width: 200px; padding: 0.5rem;">
                                <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}" style="border-radius: 6px;"><i class="fa-regular fa-user me-2 text-muted"></i> Profile</a></li>
                                <li><hr class="dropdown-divider" style="margin: 0.5rem 0;"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger" style="border-radius: 6px;"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>



        <!-- Page Content -->
        <main class="{{ $containerClass ?? 'container mb-5' }}">
            {{ $slot }}
        </main>

    </body>
</html>

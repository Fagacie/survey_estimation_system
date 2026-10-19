<x-app-layout containerClass="w-full px-4 py-6 sm:px-8 sm:py-8">
    <x-slot name="header">{{ $project->name }}</x-slot>

    <link rel="stylesheet" href="{{ asset('css/project-overview.css') }}?v={{ filemtime(public_path('css/project-overview.css')) }}">

    <div class="project-overview-page">
        <!-- BREADCRUMB -->
        <nav class="pw-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('projects.index') }}">
                <i class="fa-solid fa-folder-tree"></i> Projects
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">{{ $project->name }}</span>
            <span class="breadcrumb-separator">/</span>
            <span class="text-slate-400 font-medium">Workspace</span>
        </nav>

        <!-- FLASH NOTIFICATIONS -->
        @if (session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3 mb-6 flex items-start gap-3 text-sm rounded-lg shadow-sm">
                <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 text-red-800 border border-red-200 px-4 py-3 mb-6 flex items-start gap-3 text-sm rounded-lg shadow-sm">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 text-red-600"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @php
            $statusKey = strtolower($project->status ?? 'draft');
            $summary = $project->modellingSummary;
            $surveyCount = $project->surveyLocations->count();
            $mappedCount = $project->surveyLocations->where('status', 'Mapped')->count();
        @endphp

        <!-- 1. PROJECT HERO CARD -->
        <header class="pw-hero-card">
            <div class="pw-hero-top">
                <div class="pw-hero-identity">
                    <div class="pw-meta-pills">
                        <span class="pw-code-tag">
                            <i class="fa-solid fa-hashtag"></i>
                            {{ $project->number ?? 'PRJ-' . str_pad($project->project_Id, 4, '0', STR_PAD_LEFT) }}
                        </span>

                        <span class="pw-status-pill pw-status-pill--{{ $statusKey }}">
                            <span class="pw-status-dot"></span>
                            {{ ucfirst($project->status ?? 'draft') }}
                        </span>
                    </div>

                    <h1 class="pw-hero-title">{{ $project->name }}</h1>

                    <div class="pw-tags-row">
                        <span class="pw-tag">
                            <i class="fa-solid fa-layer-group text-blue-500"></i>
                            {{ \App\Models\Project::TYPES[$project->project_type] ?? $project->project_type ?? 'Hydrographic' }}
                        </span>

                        @if ($project->location)
                            <span class="pw-tag-divider">·</span>
                            <span class="pw-tag">
                                <i class="fa-solid fa-location-dot text-rose-500"></i>
                                {{ $project->location }}
                            </span>
                        @endif

                        @if ($project->period)
                            <span class="pw-tag-divider">·</span>
                            <span class="pw-tag">
                                <i class="fa-regular fa-clock text-amber-500"></i>
                                {{ $project->period }}
                            </span>
                        @endif

                        <span class="pw-tag-divider">·</span>
                        <span class="pw-tag">
                            <i class="fa-regular fa-calendar text-slate-400"></i>
                            Created {{ $project->created_at?->format('d M Y') ?? 'N/A' }}
                        </span>
                    </div>
                </div>

                <div class="pw-hero-actions">
                    <a href="{{ route('projects.index') }}" class="pw-btn pw-btn--secondary">
                        <i class="fa-solid fa-folder-tree"></i>
                        <span>Project Register</span>
                    </a>

                    <a href="{{ route('projects.edit', $project->project_Id) }}" class="pw-btn pw-btn--primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                        <span>Edit Details</span>
                    </a>
                </div>
            </div>
        </header>

        <!-- 2. TELEMETRY & KPI METRIC TILES -->
        <section class="pw-kpi-grid" aria-label="Project Key Indicators">
            <!-- Client Info Tile -->
            <div class="pw-kpi-tile">
                <div class="pw-kpi-icon">
                    <i class="fa-regular fa-building"></i>
                </div>
                <div class="pw-kpi-content">
                    <span class="pw-kpi-label">Client Entity</span>
                    <span class="pw-kpi-value" title="{{ $project->client?->company_name ?? 'No client assigned' }}">
                        {{ $project->client?->company_name ?? 'No client assigned' }}
                    </span>
                    <span class="pw-kpi-subtext" title="{{ $project->pic_name ?? 'No PIC specified' }}">
                        {{ $project->pic_name ? 'PIC: ' . $project->pic_name : 'No PIC assigned' }}
                    </span>
                </div>
            </div>

            <!-- Survey Areas Tile -->
            <div class="pw-kpi-tile">
                <div class="pw-kpi-icon pw-kpi-icon--teal">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div class="pw-kpi-content">
                    <span class="pw-kpi-label">Survey Areas</span>
                    <span class="pw-kpi-value">{{ $surveyCount }}</span>
                    <span class="pw-kpi-subtext">
                        {{ $project->surveyLocations->where('survey_type', 'sbes')->count() }} SBES · {{ $project->surveyLocations->where('survey_type', 'drone')->count() }} Drone
                    </span>
                </div>
            </div>

            <!-- Mapping Coverage Tile -->
            <div class="pw-kpi-tile">
                <div class="pw-kpi-icon pw-kpi-icon--emerald">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div class="pw-kpi-content">
                    <span class="pw-kpi-label">Mapped Coverage</span>
                    <span class="pw-kpi-value">{{ $mappedCount }} of {{ $surveyCount }}</span>
                    <span class="pw-kpi-subtext">
                        {{ $surveyCount > 0 ? round(($mappedCount / $surveyCount) * 100) . '% boundaries finalized' : 'No areas added yet' }}
                    </span>
                </div>
            </div>

            <!-- 3D Modelling Tile -->
            <div class="pw-kpi-tile">
                <div class="pw-kpi-icon pw-kpi-icon--indigo">
                    <i class="fa-solid fa-cubes"></i>
                </div>
                <div class="pw-kpi-content">
                    <span class="pw-kpi-label">3D Modelling</span>
                    <span class="pw-kpi-value">
                        {{ $summary ? 'RM ' . number_format($summary->grand_total, 2) : 'Not Added' }}
                    </span>
                    <span class="pw-kpi-subtext">
                        {{ $summary ? ($summary->package_name ?? 'Custom Package') : 'Volumetric processing' }}
                    </span>
                </div>
            </div>
        </section>

        <!-- 3. WORKFLOW LAUNCHPAD (ADD WORKFLOWS) -->
        <section class="pw-section" aria-labelledby="launchpad-title">
            <div class="pw-section-header">
                <div class="pw-section-title-wrap">
                    <h2 id="launchpad-title">Survey Modules & Workflows</h2>
                    <p>Initialize hydrographic soundings, aerial photogrammetry, or 3D volume modelling packages.</p>
                </div>
                <a href="{{ url('/admin/equipment') }}" class="pw-btn pw-btn--secondary pw-btn--sm">
                    <i class="fa-solid fa-database text-slate-400"></i>
                    <span>Equipment Catalogue</span>
                </a>
            </div>

            <div class="pw-launchpad-grid">
                <!-- SBES Bathymetry Card -->
                <div class="pw-launchpad-card">
                    <div class="pw-launchpad-card-top">
                        <div class="pw-launchpad-badge-icon">
                            <i class="fa-solid fa-water"></i>
                        </div>
                        <div class="pw-launchpad-info">
                            <h3>Single Beam (SBES)</h3>
                            <p>Hydrographic sounding area with bathymetric lines and depth intervals.</p>
                        </div>
                    </div>
                    <button type="button" onclick="openSurveyModal('sbes')" class="pw-launchpad-btn">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add SBES Area</span>
                    </button>
                </div>

                <!-- Drone Mapping Card -->
                <div class="pw-launchpad-card">
                    <div class="pw-launchpad-card-top">
                        <div class="pw-launchpad-badge-icon pw-launchpad-badge-icon--blue">
                            <i class="fa-solid fa-plane-up"></i>
                        </div>
                        <div class="pw-launchpad-info">
                            <h3>Drone Mapping</h3>
                            <p>Aerial photogrammetry, orthomosaic imaging, and DEM surface mapping.</p>
                        </div>
                    </div>
                    <button type="button" onclick="openSurveyModal('drone')" class="pw-launchpad-btn">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Drone Area</span>
                    </button>
                </div>

                <!-- 3D Modelling Card -->
                @if (!$summary)
                    <div class="pw-launchpad-card">
                        <div class="pw-launchpad-card-top">
                            <div class="pw-launchpad-badge-icon pw-launchpad-badge-icon--indigo">
                                <i class="fa-solid fa-cubes"></i>
                            </div>
                            <div class="pw-launchpad-info">
                                <h3>3D Processing & Modelling</h3>
                                <p>Volumetric calculations, mesh reconstruction, and CAD deliverables.</p>
                            </div>
                        </div>
                        <a href="{{ route('projects.modeling.builder', ['project_id' => $project->project_Id]) }}" class="pw-launchpad-btn">
                            <i class="fa-solid fa-sliders"></i>
                            <span>Configure Package</span>
                        </a>
                    </div>
                @else
                    <div class="pw-launchpad-card pw-launchpad-card--configured">
                        <div class="pw-launchpad-card-top">
                            <div class="pw-launchpad-badge-icon">
                                <i class="fa-solid fa-cubes"></i>
                            </div>
                            <div class="pw-launchpad-info">
                                <h3>3D Modelling (Configured)</h3>
                                <p>Package active: {{ $summary->package_name ?? 'Custom selection' }} (RM {{ number_format($summary->grand_total, 2) }}).</p>
                            </div>
                        </div>
                        <a href="{{ route('projects.modeling.builder', ['project_id' => $project->project_Id]) }}" class="pw-launchpad-btn">
                            <i class="fa-solid fa-sliders"></i>
                            <span>Manage Modules</span>
                        </a>
                    </div>
                @endif
            </div>
        </section>

        <!-- 4. CONFIGURED WORKFLOWS REGISTER -->
        <section class="pw-section" aria-labelledby="register-title">
            <div class="pw-register-card">
                <div class="pw-register-header">
                    <div class="pw-register-heading">
                        <h3 id="register-title">Configured Workflows</h3>
                        <span class="pw-count-badge">
                            {{ $surveyCount + ($summary ? 1 : 0) }} Active
                        </span>
                    </div>

                    @if ($surveyCount > 1)
                        <a href="{{ route('projects.report.preview', $project->project_Id) }}" class="pw-btn pw-btn--secondary pw-btn--sm">
                            <i class="fa-solid fa-file-lines text-slate-400"></i>
                            <span>Full Project Report (All Areas)</span>
                        </a>
                    @endif
                </div>

                <ul class="pw-workflow-list">
                    @php $hasAnyModule = false; @endphp

                    <!-- Survey Areas List -->
                    @foreach ($project->surveyLocations as $location)
                        @php $hasAnyModule = true; @endphp
                        <li class="pw-workflow-item">
                            <div class="pw-workflow-left">
                                @if ($location->survey_type === 'drone')
                                    <div class="pw-workflow-icon pw-workflow-icon--blue">
                                        <i class="fa-solid fa-plane-up"></i>
                                    </div>
                                @else
                                    <div class="pw-workflow-icon">
                                        <i class="fa-solid fa-water"></i>
                                    </div>
                                @endif

                                <div class="pw-workflow-details">
                                    <div class="pw-workflow-name-row">
                                        <span class="pw-workflow-name">
                                            {{ $location->survey_type === 'drone' ? 'Drone Mapping' : 'SBES Survey' }}: {{ $location->name }}
                                        </span>

                                        @if ($location->status === 'Mapped')
                                            <span class="pw-status-pill pw-status-pill--completed">
                                                <span class="pw-status-dot"></span> Mapped
                                            </span>
                                        @else
                                            <span class="pw-status-pill pw-status-pill--draft">
                                                <span class="pw-status-dot"></span> Pending Planning
                                            </span>
                                        @endif
                                    </div>

                                    <span class="pw-workflow-meta">
                                        Added {{ $location->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="pw-row-actions">
                                <a href="{{ route('projects.report.preview', ['project' => $project->project_Id, 'location_id' => $location->id]) }}"
                                   class="pw-action-btn"
                                   title="Preview Survey Report">
                                    <i class="fa-regular fa-file-pdf"></i>
                                    <span>Report</span>
                                </a>

                                <a href="{{ route('projects.surveys.map', [$project->project_Id, $location->id]) }}"
                                   class="pw-action-btn pw-action-btn--map"
                                   title="Open Survey Boundary & Planning Map">
                                    <i class="fa-solid fa-map"></i>
                                    <span>Planning Map</span>
                                </a>

                                <a href="{{ route('quotation.index', ['project_id' => $project->project_Id, 'module' => $location->survey_type, 'location_id' => $location->id]) }}"
                                   class="pw-action-btn pw-action-btn--quote"
                                   title="Open Commercial Quotation">
                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                    <span>Quotation</span>
                                </a>

                                <form action="{{ route('projects.surveys.destroy', [$project->project_Id, $location->id]) }}"
                                      method="POST"
                                      class="m-0"
                                      onsubmit="return confirm('Are you sure you want to delete this survey area?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="pw-action-btn pw-action-btn--delete"
                                            aria-label="Delete survey area {{ $location->name }}"
                                            title="Delete Survey Area">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endforeach

                    <!-- 3D Modelling Summary Row -->
                    @if ($summary)
                        @php $hasAnyModule = true; @endphp
                        <li class="pw-workflow-item">
                            <div class="pw-workflow-left">
                                <div class="pw-workflow-icon pw-workflow-icon--indigo">
                                    <i class="fa-solid fa-cubes"></i>
                                </div>
                                <div class="pw-workflow-details">
                                    <div class="pw-workflow-name-row">
                                        <span class="pw-workflow-name">
                                            3D Modelling: {{ $summary->package_name ?? 'Custom Selection' }}
                                        </span>
                                        <span class="pw-status-pill pw-status-pill--completed">
                                            <span class="pw-status-dot"></span> Configured
                                        </span>
                                    </div>
                                    <span class="pw-workflow-meta">
                                        {{ $project->modellingItems->pluck('catalog_module_id')->unique()->count() }} modules active · Total: RM {{ number_format($summary->grand_total, 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="pw-row-actions">
                                <a href="{{ route('projects.modeling.builder', ['project_id' => $project->project_Id]) }}"
                                   class="pw-action-btn"
                                   title="Edit 3D Modelling Packages">
                                    <i class="fa-solid fa-sliders"></i>
                                    <span>Edit Modules</span>
                                </a>

                                <a href="{{ route('quotation.index', ['project_id' => $project->project_Id, 'module' => 'modelling']) }}"
                                   class="pw-action-btn pw-action-btn--quote"
                                   title="Open Commercial Quotation">
                                    <i class="fa-solid fa-file-invoice-dollar"></i>
                                    <span>Quotation</span>
                                </a>

                                <form action="{{ route('projects.modeling.destroy', $project->project_Id) }}"
                                      method="POST"
                                      class="m-0"
                                      onsubmit="return confirm('Are you sure you want to remove 3D modelling from this project?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="pw-action-btn pw-action-btn--delete"
                                            aria-label="Delete 3D Modelling"
                                            title="Delete Modelling">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    @endif

                    <!-- Empty State -->
                    @if (!$hasAnyModule)
                        <li class="pw-empty-state">
                            <div class="pw-empty-icon">
                                <i class="fa-regular fa-folder-open"></i>
                            </div>
                            <h4 class="pw-empty-title">No workflows configured yet</h4>
                            <p class="pw-empty-text">
                                Get started by adding a Single Beam (SBES) bathymetry polygon, Drone mapping mission, or 3D processing package from the launchpad above.
                            </p>
                        </li>
                    @endif
                </ul>
            </div>
        </section>

        <!-- 5. NEW SURVEY AREA MODAL -->
        <div class="modal fade" id="newSurveyModal" tabindex="-1" aria-labelledby="modal_title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content pw-modal-content">
                    <form action="{{ route('projects.surveys.store', $project->project_Id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="survey_type" id="modal_survey_type" value="sbes">

                        <div class="pw-modal-header">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm" id="modal_icon_badge">
                                    <i class="fa-solid fa-water" id="modal_icon"></i>
                                </div>
                                <h5 id="modal_title">New Survey Area</h5>
                            </div>
                            <button type="button" class="pw-modal-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div class="pw-modal-body">
                            <div>
                                <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                                    Area / Polygon Designation <span class="text-rose-500">*</span>
                                </label>
                                <input type="text"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all"
                                       id="name"
                                       name="name"
                                       placeholder="e.g. Terengganu River Basin, Sector A"
                                       required>
                                <p class="text-xs text-slate-400 mt-1.5">You will define latitude & longitude coordinates or draw survey boundaries on the map in the next step.</p>
                            </div>
                        </div>

                        <div class="pw-modal-footer">
                            <button type="button" class="pw-btn pw-btn--secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="pw-btn pw-btn--primary">
                                <span>Create Survey Area</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- 6. SURVEY MODAL CONTROLLER SCRIPT -->
        <script>
            function openSurveyModal(type) {
                const hiddenInput = document.getElementById('modal_survey_type');
                const modalElement = document.getElementById('newSurveyModal');
                const modalTitle = document.getElementById('modal_title');
                const modalIcon = document.getElementById('modal_icon');
                const nameInput = document.getElementById('name');

                if (hiddenInput) {
                    hiddenInput.value = type;
                }

                if (type === 'drone') {
                    if (modalTitle) modalTitle.innerText = 'New Drone Mapping Area';
                    if (modalIcon) modalIcon.className = 'fa-solid fa-plane-up';
                    if (nameInput) nameInput.placeholder = 'e.g. North Ridge Topography, Sector 1';
                } else {
                    if (modalTitle) modalTitle.innerText = 'New SBES Survey Area';
                    if (modalIcon) modalIcon.className = 'fa-solid fa-water';
                    if (nameInput) nameInput.placeholder = 'e.g. Main Navigation Channel, Sector A';
                }

                if (modalElement && typeof bootstrap !== 'undefined') {
                    let modal = bootstrap.Modal.getInstance(modalElement);
                    if (!modal) {
                        modal = new bootstrap.Modal(modalElement);
                    }
                    modal.show();
                    setTimeout(() => {
                        if (nameInput) nameInput.focus();
                    }, 300);
                }
            }
        </script>
    </div>
</x-app-layout>

<x-app-layout hideSidebar="true" containerClass="container-fluid px-0 m-0 relative h-full">
    <x-slot name="header">{{ $project->name }} — Map Planning</x-slot>

    <link rel="stylesheet" href="{{ asset('css/map-workspace.css') }}?v={{ time() }}">
    <style>
        /* Override Tailwind's .collapse (visibility: collapse) which conflicts with Bootstrap */
        .workspace-sidebar .collapse, 
        .workspace-sidebar .collapse.show {
            visibility: visible !important;
        }
    </style>
    <!-- dom-to-image for map screenshot capture (better Leaflet support than html2canvas) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>

    <div class="workspace-container h-[calc(100vh-64px)] overflow-hidden">
        <!-- ============================================================
             SIDEBAR
             ============================================================ -->
        <div class="workspace-sidebar planning-sidebar">

            <!-- KEY METRIC HIGHLIGHT STRIP (Always Visible) -->
            @if($surveyLocation->survey_type !== 'drone')
            <div class="metric-strip">
                <div class="metric-grid">
                    <div class="metric-item">
                        <div class="metric-label">Distance</div>
                        <div class="metric-value c-cyan" id="ms-total-nm">0.00 NM</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Lines</div>
                        <div class="metric-value c-green" id="ms-total-lines">0</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Hours</div>
                        <div class="metric-value c-amber" id="ms-survey-hours">0.0</div>
                    </div>
                    <div class="metric-item">
                        <div class="metric-label">Days</div>
                        <div class="metric-value c-rose" id="ms-working-days">0.0</div>
                    </div>
                </div>
            </div>
            @endif

            <!-- SCROLLABLE ACCORDION CONTENT -->
            <div class="sidebar-content">
                <div class="accordion ws-accordion" id="sidebarAccordion">



                    <!-- 2. BOUNDARY -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button accent-green" type="button" data-bs-toggle="collapse" data-bs-target="#panelBoundary">
                                <span class="panel-icon bg-green"><i class="fa-solid fa-draw-polygon"></i></span>
                                Boundary
                            </button>
                        </h2>
                        <div id="panelBoundary" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <div class="stat-row">
                                    <span class="stat-label">Area</span>
                                    <span class="stat-value" id="stat-boundary-area">0 m²</span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Perimeter</span>
                                    <span class="stat-value" id="stat-boundary-perimeter">0 m</span>
                                </div>
                                <p class="mt-2 mb-2" style="font-size: 0.7rem; color: var(--sidebar-muted);">
                                    <i class="fa-solid fa-info-circle"></i> Use the polygon tool on the map to draw the survey boundary.
                                </p>
                                <div class="d-flex flex-column gap-2 mt-2">
                                    <button type="button" id="btn-open-coord-modal" class="btn btn-ws btn-ws-secondary w-100" data-bs-toggle="modal" data-bs-target="#coordInputModal">
                                        <i class="fa-solid fa-keyboard me-1"></i> Enter Coordinates
                                    </button>
                                    <button type="button" id="btn-clear-boundary" class="btn btn-ws btn-ws-outline-danger w-100">
                                        <i class="fa-solid fa-trash-can me-1"></i> Clear Boundary
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. LINE GENERATOR -->
                    @if($surveyLocation->survey_type !== 'drone')
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button accent-cyan" type="button" data-bs-toggle="collapse" data-bs-target="#panelGenerator">
                                <span class="panel-icon bg-cyan"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                                Line Generator
                            </button>
                        </h2>
                        <div id="panelGenerator" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                <div class="mb-3">
                                    <label class="form-label-panel">Generation Mode</label>
                                    <select id="gen-mode" class="form-select form-control-panel" style="font-size: 0.8rem;">
                                        <option value="polygon">Polygon Fill</option>
                                        <option value="centerline">Centerline (Custom)</option>
                                    </select>
                                </div>

                                <!-- Polygon Mode Settings -->
                                <div id="gen-mode-polygon">
                                    <div class="section-label" style="color: var(--accent-blue);"><i class="fa-solid fa-minus"></i> Main Lines</div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label-panel">Spacing (m)</label>
                                            <input type="number" id="gen-spacing" class="form-control-panel" value="{{ $surveyLocation->surveyGenerationSetting->line_spacing ?? 100 }}" min="1">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label-panel">Angle (deg)</label>
                                            <input type="number" id="gen-angle" class="form-control-panel" value="{{ $surveyLocation->surveyGenerationSetting->orientation_angle ?? 90 }}" min="0" max="360">
                                        </div>
                                    </div>
                                </div>

                                <!-- Centerline Mode Settings -->
                                <div id="gen-mode-centerline" class="d-none">
                                    <div class="section-label" style="color: var(--accent-blue);"><i class="fa-solid fa-minus"></i> Centerline Offsets</div>
                                    <div class="row g-2 mb-2">
                                        <div class="col-12">
                                            <label class="form-label-panel">Spacing (m)</label>
                                            <input type="number" id="gen-cl-spacing" class="form-control-panel" value="50" min="1">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label-panel">Lines Left (Port)</label>
                                            <input type="number" id="gen-cl-left" class="form-control-panel" value="2" min="0">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label-panel">Lines Right (Stbd)</label>
                                            <input type="number" id="gen-cl-right" class="form-control-panel" value="2" min="0">
                                        </div>
                                    </div>
                                    <p class="mb-2" style="font-size: 0.7rem; color: var(--sidebar-muted);">
                                        <i class="fa-solid fa-info-circle"></i> Draw a single polyline on the map to act as the centerline.
                                    </p>
                                </div>

                                <button id="btn-generate-main" class="btn btn-ws btn-ws-primary mb-2">
                                    <i class="fa-solid fa-bars-staggered"></i> Generate Main Lines
                                </button>

                                <!-- Cross / Tie Lines -->
                                <div class="section-label mt-3" style="color: var(--accent-amber);"><i class="fa-solid fa-grip-lines-vertical"></i> Cross / Tie Lines</div>
                                <div class="row g-2 mb-2">
                                    <div class="col-12">
                                        <label class="form-label-panel">Spacing (m)</label>
                                        <input type="number" id="gen-cross-spacing" class="form-control-panel" value="{{ $surveyLocation->surveyGenerationSetting->cross_line_spacing ?? '' }}" placeholder="Optional" min="1">
                                    </div>
                                </div>
                                <button id="btn-generate-cross" class="btn btn-ws btn-ws-secondary mb-2">
                                    <i class="fa-solid fa-grip-lines-vertical"></i> Generate Cross / Tie Lines
                                </button>

                                <div id="stale-warning" class="stale-warning d-none mt-2">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Parameters changed. Regenerate lines.
                                </div>

                                <button id="btn-clear-lines" class="btn btn-ws btn-ws-outline-danger mt-2">
                                    <i class="fa-solid fa-eraser"></i> Clear All Lines
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 4. ENGINEERING CALCULATION SUMMARY (Survey Statistics) -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button accent-purple" type="button" data-bs-toggle="collapse" data-bs-target="#panelStats">
                                <span class="panel-icon bg-purple"><i class="fa-solid fa-calculator"></i></span>
                                Engineering Calculation
                            </button>
                        </h2>
                        <div id="panelStats" class="accordion-collapse collapse show">
                            <div class="accordion-body" style="font-size: 0.85rem; padding: 10px;">
                                
                                <!-- SURVEY GEOMETRY -->
                                <div class="mb-2 fw-bold text-uppercase" style="color: var(--sb-text-muted); font-size: 0.75rem; letter-spacing: 0.5px;">Survey Geometry</div>
                                <div class="stat-row"><span class="stat-label">Survey Area</span><span class="stat-value" id="stat-eng-boundary-area">0 m²</span></div>
                                <div class="stat-row"><span class="stat-label">Boundary Perimeter</span><span class="stat-value" id="stat-eng-boundary-perimeter">0 km</span></div>
                                <div class="stat-row"><span class="stat-label">Survey Width (Grid)</span><span class="stat-value" id="stat-survey-width">0.00 km</span></div>
                                <div class="stat-row"><span class="stat-label">Survey Length (Grid)</span><span class="stat-value" id="stat-survey-length">0.00 km</span></div>
                                
                                <!-- MAIN SURVEY LINES -->
                                <div class="mt-3 mb-2 fw-bold text-uppercase" style="color: var(--sb-text-muted); font-size: 0.75rem; letter-spacing: 0.5px;">Main Survey Lines</div>
                                <div class="stat-row"><span class="stat-label">Number of Main Lines</span><span class="stat-value" id="stat-main-count">0</span></div>
                                <div class="stat-row"><span class="stat-label">Main Survey Distance (km)</span><span class="stat-value" id="stat-main-dist-km">0.00 km</span></div>
                                <div class="stat-row"><span class="stat-label">Main Survey Distance (NM)</span><span class="stat-value" id="stat-main-dist-nm">0.00 NM</span></div>
                                
                                <!-- CROSSLINES -->
                                <div class="mt-3 mb-2 fw-bold text-uppercase" style="color: var(--sb-text-muted); font-size: 0.75rem; letter-spacing: 0.5px;">Crosslines</div>
                                <div class="stat-row"><span class="stat-label">Number of Crosslines</span><span class="stat-value" id="stat-cross-count">0</span></div>
                                <div class="stat-row"><span class="stat-label">Total Crossline Distance (km)</span><span class="stat-value" id="stat-cross-dist-km">0.00 km</span></div>
                                <div class="stat-row"><span class="stat-label">Total Crossline Distance (NM)</span><span class="stat-value" id="stat-cross-dist-nm">0.00 NM</span></div>

                                <!-- TOTAL SURVEY DISTANCE -->
                                <div class="mt-3 pt-2" style="border-top: 1px solid var(--sb-border);">
                                    <div class="stat-row">
                                        <span class="stat-label fw-bold">TOTAL SURVEY DISTANCE</span>
                                        <span class="stat-value highlight" id="stat-total-nm" style="color: var(--accent-green); font-size: 1.05rem;">0.00 NM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- 5. SURVEY PARAMETERS & TIME -->
                    @if($surveyLocation->survey_type !== 'drone')
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button accent-amber" type="button" data-bs-toggle="collapse" data-bs-target="#panelTime">
                                <span class="panel-icon bg-amber"><i class="fa-solid fa-clock"></i></span>
                                Survey Parameters & Time
                            </button>
                        </h2>
                        <div id="panelTime" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                
                                <!-- SURVEY CALCULATION PARAMETERS -->
                                <div class="mb-2 fw-bold text-uppercase" style="color: var(--sb-text-muted); font-size: 0.75rem; letter-spacing: 0.5px;">SBES Calculation Parameters</div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label-panel mb-0" title="Survey vessel speed">Survey Speed (knots)</label>
                                        <input type="number" step="0.1" id="sbes-speed" class="form-control-panel mt-1" value="{{ $surveyLocation->sbesParameters?->survey_speed_knots ?? '5.0' }}" onchange="calculateTimeEstimation()">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label-panel mb-0" title="Operating hours per day">Working Hrs / Day</label>
                                        <input type="number" step="0.1" id="sbes-workhrs" class="form-control-panel mt-1" value="{{ $surveyLocation->sbesParameters?->working_hours_per_day ?? '8.0' }}" onchange="calculateTimeEstimation()">
                                    </div>
                                    <div class="col-12 mt-3 fw-bold text-uppercase" style="color: var(--sb-text-muted); font-size: 0.75rem; letter-spacing: 0.5px;">Global Allowances</div>
                                    <div class="col-4 mt-2">
                                        <label class="form-label-panel mb-0" title="Weather Standby Days">Weather</label>
                                        <input type="number" step="0.1" id="project-weather" class="form-control-panel mt-1" value="{{ $project->weather_days ?? '0' }}" onchange="calculateTimeEstimation()">
                                    </div>
                                    <div class="col-4 mt-2">
                                        <label class="form-label-panel mb-0" title="Mobilization / Demobilization">Mob / Demob</label>
                                        <input type="number" step="0.1" id="project-mob" class="form-control-panel mt-1" value="{{ $project->mod_demod_days ?? '0' }}" onchange="calculateTimeEstimation()">
                                    </div>
                                    <div class="col-4 mt-2">
                                        <label class="form-label-panel mb-0" title="Patch Test Days">Patch Test</label>
                                        <input type="number" step="0.1" id="project-patch" class="form-control-panel mt-1" value="{{ $project->patch_test_days ?? '0' }}" onchange="calculateTimeEstimation()">
                                    </div>
                                </div>
                                
                                <!-- SURVEY TIME (OUTPUT) -->
                                <div class="stat-row"><span class="stat-label fw-bold">Total Survey Time</span><span class="stat-value" id="calc-survey-hours" style="color: var(--accent-amber); font-weight: bold;">0.00 hours</span></div>
                                <div class="stat-row mb-3"><span class="stat-label fw-bold">ESTIMATED SURVEY DAYS</span><span class="stat-value highlight" id="calc-working-days" style="color: var(--accent-amber);">0.00 days</span></div>


                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- 6. DRONE MAPPING -->
                    @if($surveyLocation->survey_type === 'drone')
                    <div class="accordion-item" id="droneMappingAccordionItem">
                        <h2 class="accordion-header">
                            <button class="accordion-button accent-green" type="button" data-bs-toggle="collapse" data-bs-target="#panelDrone">
                                <span class="panel-icon bg-green"><i class="fa-solid fa-plane"></i></span>
                                Drone Mapping
                            </button>
                        </h2>
                        <div id="panelDrone" class="accordion-collapse collapse show">
                            <div class="accordion-body p-0" id="drone-ui-container">
                                <!-- The drone UI will be rendered here by DroneUI.init() -->
                            </div>
                        </div>
                    </div>
                    @endif

                </div><!-- end accordion -->


            </div><!-- end sidebar-content -->

            <!-- STICKY BOTTOM ACTIONS -->
            <div class="p-3 border-t border-slate-200 bg-slate-50 flex-shrink-0" style="border-top: 1px solid var(--sb-border);">
                <button class="btn-save-planning-trigger btn btn-dark w-100 mb-2 fw-bold d-flex justify-content-center align-items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save Map Planning
                </button>
                <a href="{{ route('projects.report.preview', ['project' => $project->project_Id, 'location_id' => $surveyLocation->id]) }}" class="btn btn-outline-secondary w-100 mb-2 fw-semibold d-flex justify-content-center align-items-center gap-2">
                    <i class="fa-solid fa-file-lines"></i> Survey Report
                </a>
                <a href="{{ route('projects.show', $project->project_Id) }}" class="btn btn-outline-secondary w-100 fw-semibold d-flex justify-content-center align-items-center gap-2">
                    Exit Map
                </a>
            </div>
        </div>

        <!-- ============================================================
             MAP CONTAINER (Full Screen)
             ============================================================ -->
        <div class="workspace-map">
            <button id="btn-toggle-planning-panel" class="map-panel-toggle" aria-expanded="true" aria-label="Hide planning controls">
                <i class="fa-solid fa-sliders" aria-hidden="true"></i>
                <span>Hide controls</span>
            </button>

            <!-- MAP FLOATING ACTIONS REMOVED (Moved to sidebar) -->

            @if($surveyLocation->survey_type === 'drone')
            <!-- FLOATING MISSION ESTIMATE (Drone) -->
            <div class="position-absolute" style="z-index: 1000; top: 15px; left: 50%; transform: translateX(-50%); pointer-events: none;">
                <div class="bg-white rounded shadow-sm border p-2 d-flex gap-3 align-items-center" style="pointer-events: auto; background-color: rgba(255,255,255,0.95) !important;">
                    <div class="fw-bold pe-2 border-end" style="font-size: 0.8rem; color: var(--accent-orange);"><i class="fa-solid fa-plane"></i> Estimate</div>
                    <div style="font-size: 0.75rem;"><span class="text-muted">Lines:</span> <span id="float-lines" class="fw-bold">0</span></div>
                    <div style="font-size: 0.75rem;"><span class="text-muted">Images:</span> <span id="float-images" class="fw-bold">0</span></div>
                    <div style="font-size: 0.75rem;"><span class="text-muted">Dist:</span> <span id="float-distance" class="fw-bold">0 m</span></div>
                    <div style="font-size: 0.75rem;"><span class="text-muted">Time:</span> <span id="float-duration" class="fw-bold text-success">0m 0s</span></div>
                    <div style="font-size: 0.75rem;"><span class="text-muted">Sorties:</span> <span id="float-sorties" class="fw-bold" style="color: var(--accent-purple);">0</span></div>
                </div>
            </div>
            @endif

            <!-- FLOATING CONTROLS: TOP-RIGHT (Basemap + Feature Layers) -->
            <div class="map-floating-controls-top-right">
                <div class="map-basemap-control">
                    <button type="button" class="map-basemap-btn active" data-basemap="osm">Street</button>
                    <button type="button" class="map-basemap-btn" data-basemap="satellite">Satellite</button>
                    <button type="button" class="map-basemap-btn" data-basemap="ocean">Ocean</button>
                    <button type="button" class="map-basemap-btn" data-basemap="dark">Dark</button>
                </div>

                <div class="map-layer-control">
                    <div class="layer-title">Feature Layers</div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="layer-boundaries" checked>
                        <label class="form-check-label" for="layer-boundaries">
                            <span class="layer-color-dot" style="background: var(--boundary-color, #2563eb);"></span> Boundaries
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="layer-main-lines" checked>
                        <label class="form-check-label" for="layer-main-lines">
                            <span class="layer-color-dot" style="background: var(--main-line-color, #f43f5e);"></span> Main Lines
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="layer-cross-lines" checked>
                        <label class="form-check-label" for="layer-cross-lines">
                            <span class="layer-color-dot" style="background: var(--cross-line-color, #f59e0b);"></span> Cross Lines
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="layer-labels">
                        <label class="form-check-label" for="layer-labels">
                            <span class="layer-color-dot" style="background: #ffffff; border: 1px solid #94a3b8;"></span> Line Labels
                        </label>
                    </div>
                </div>
            </div>

            <!-- MAP LOADING OVERLAY -->
            <div id="map-loading-overlay" class="position-absolute top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center map-loading-overlay" style="z-index: 1050;">
                <div class="text-center">
                    <div class="spinner-border" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="mt-2 fw-bold" id="map-loading-text">Processing...</h5>
                </div>
            </div>

            <!-- COORDINATE BAR (bottom-right) -->
            <div class="position-absolute bottom-0 end-0 me-2 mb-4" style="z-index: 1000;">
                <div class="map-coord-bar" id="coord-display">
                    Lat: <span>0.000000</span> &nbsp; Lng: <span>0.000000</span>
                </div>
            </div>

            <div id="map"></div>
        </div>
    </div>

    <!-- ============================================================
         COORDINATE BOUNDARY INPUT MODAL
         ============================================================ -->
    <div class="modal fade" id="coordInputModal" tabindex="-1" aria-labelledby="coordInputModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content coord-modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <span class="coord-modal-badge"><i class="fa-solid fa-draw-polygon"></i></span>
                        <div>
                            <h5 class="modal-title fw-bold" id="coordInputModalLabel" style="font-size: 1.05rem; color: #0f2b3c;">Define Boundary by Coordinates</h5>
                            <p class="text-muted mb-0" style="font-size: 0.78rem;">Enter or paste latitude/longitude coordinate pairs to define your survey boundary polygon.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="btn-modal-close-x"></button>
                </div>

                <div class="modal-body pt-3 pb-2">
                    <!-- Coordinate Order Selection -->
                    <div class="coord-order-box mb-3">
                        <span class="coord-order-title"><i class="fa-solid fa-arrow-right-arrow-left me-1"></i> Coordinate Order:</span>
                        <div class="d-flex align-items-center gap-3">
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="coordOrder" id="coordOrderLatLng" value="latlng" checked>
                                <label class="form-check-label fw-semibold" for="coordOrderLatLng" style="font-size: 0.85rem; color: #1e3a5f;">
                                    Latitude, Longitude <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">Default</span>
                                </label>
                            </div>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="coordOrder" id="coordOrderLngLat" value="lnglat">
                                <label class="form-check-label fw-semibold" for="coordOrderLngLat" style="font-size: 0.85rem; color: #1e3a5f;">
                                    Longitude, Latitude <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">X, Y / GIS</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Nav Tabs: Bulk Paste vs Table Entry -->
                    <ul class="nav nav-pills coord-nav-pills mb-3" id="coordTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-bulk-btn" data-bs-toggle="pill" data-bs-target="#tab-bulk-content" type="button" role="tab" aria-selected="true">
                                <i class="fa-solid fa-paste me-1"></i> Bulk Paste
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-table-btn" data-bs-toggle="pill" data-bs-target="#tab-table-content" type="button" role="tab" aria-selected="false">
                                <i class="fa-solid fa-table-cells me-1"></i> Table Entry
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="coordTabContent">
                        <!-- Bulk Paste Tab -->
                        <div class="tab-pane fade show active" id="tab-bulk-content" role="tabpanel">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.8rem;">
                                Paste Coordinate Pairs (one pair per line, separated by comma, tab, or space):
                            </label>
                            <textarea id="coord-bulk-input" class="form-control coord-textarea" rows="7" placeholder="e.g.&#10;4.2105, 101.9758&#10;4.2105, 102.0500&#10;4.1500, 102.0500&#10;4.1500, 101.9758"></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="text-muted" style="font-size: 0.72rem;">
                                    <i class="fa-solid fa-circle-info me-1"></i> Paste directly from Excel, CSV, or text. Minimum 3 distinct vertices required.
                                </span>
                                <button type="button" id="btn-parse-to-table" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-arrow-down-long me-1"></i> Sync to Table
                                </button>
                            </div>
                        </div>

                        <!-- Table Entry Tab -->
                        <div class="tab-pane fade" id="tab-table-content" role="tabpanel">
                            <div class="table-responsive coord-table-container" style="max-height: 220px; overflow-y: auto;">
                                <table class="table table-sm table-bordered coord-table mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;" class="text-center">#</th>
                                            <th id="th-coord-1">Latitude (deg)</th>
                                            <th id="th-coord-2">Longitude (deg)</th>
                                            <th style="width: 50px;" class="text-center">Del</th>
                                        </tr>
                                    </thead>
                                    <tbody id="coord-table-body">
                                        <!-- Dynamic Rows -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <button type="button" id="btn-add-coord-row" class="btn btn-sm btn-outline-secondary" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-plus me-1"></i> Add Point
                                </button>
                                <button type="button" id="btn-sync-to-bulk" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem;">
                                    <i class="fa-solid fa-arrow-up-long me-1"></i> Sync to Bulk Text
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Validation / Status Alert -->
                    <div id="coord-validation-alert" class="coord-alert d-none mt-3">
                        <div class="d-flex align-items-start gap-2">
                            <i id="coord-validation-icon" class="fa-solid fa-circle-exclamation mt-1"></i>
                            <div>
                                <div id="coord-validation-title" class="fw-bold" style="font-size: 0.82rem;">Validation Status</div>
                                <div id="coord-validation-msg" style="font-size: 0.76rem;">Message</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" id="btn-coord-cancel" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="btn-coord-preview" class="btn btn-sm btn-outline-info px-3">
                        <i class="fa-solid fa-eye me-1"></i> Preview on Map
                    </button>
                    <button type="button" id="btn-coord-apply" class="btn btn-sm btn-primary px-3" disabled>
                        <i class="fa-solid fa-check me-1"></i> Apply Boundary
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         MAP SCRIPT
         ============================================================ -->
    <!-- DYNAMIC CACHE BUSTER FOR MATH ENGINE -->
    <script src="{{ asset('js/survey-math.js') }}?v={{ time() }}"></script>
    <script>
        window.DB_DRONES = @json($activeDrones ?? []);
        window.DB_CAMERAS = @json($activeCameras ?? []);
    </script>
    <script src="{{ asset('js/drone_mapping/photogrammetry.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/drone_mapping/ui.js') }}?v={{ time() }}"></script>

    <script>
        window.MAP_WORKSPACE_CONFIG = {
            surveyType: "{{ $surveyLocation->survey_type }}",
            projectId: {{ $project->project_Id }},
            projectLocation: @json($project->location),
            equipmentUrl: "{{ url('/admin/equipment') }}",
            droneParameters: {!! json_encode($surveyLocation->droneMappingParameters) !!},
            boundaries: {!! $surveyLocation->boundaries->map(fn($b) => $b->geometry)->toJson() !!},
            linesUrl: "{{ route('projects.surveys.lines', [$project->project_Id, $surveyLocation->id]) }}",
            saveMapUrl: "{{ route('projects.surveys.map.save', [$project->project_Id, $surveyLocation->id]) }}",
            saveParametersUrl: "{{ route('projects.surveys.parameters.store', [$project->project_Id, $surveyLocation->id]) }}",
            screenshotUrl: "{{ route('projects.surveys.map.screenshot', [$project->project_Id, $surveyLocation->id]) }}",
            reportPreviewUrl: "{{ route('projects.report.preview', ['project' => $project->project_Id, 'location_id' => $surveyLocation->id]) }}",
            exitMapUrl: "{{ route('projects.show', $project->project_Id) }}",
            gisWorkerUrl: @json(asset('js/gis-worker.js'))
        };
    </script>
    <script src="{{ asset('js/ises/map-workspace.js') }}?v={{ time() }}"></script>
</x-app-layout>

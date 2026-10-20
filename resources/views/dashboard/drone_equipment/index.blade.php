<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Drone & Camera Equipment</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/drone-equipment.css') }}?v={{ filemtime(public_path('css/drone-equipment.css')) }}">
    @endpush

    <div class="equipment-page pb-12">
        <!-- Breadcrumb Navigation -->
        <nav class="eq-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
            <a href="{{ route('admin.index') }}">Item Catalog</a>
            <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
            <span class="breadcrumb-current">Drone Equipment</span>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: var(--eq-radius-sm); border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534;">
                <i class="fa-solid fa-circle-check me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: var(--eq-radius-sm); border: 1px solid #fecaca; background: #fef2f2; color: #991b1b;">
                <div class="d-flex align-items-center mb-1 font-bold">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <strong>Please check the errors below:</strong>
                </div>
                <ul class="mb-0 ps-4 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header -->
        <header class="eq-header">
            <div class="eq-header-title-wrap">
                <h1>Drone Mapping Equipment</h1>
                <p>Manage aircraft airframes and photogrammetric camera sensors calibrated for aerial mapping surveys.</p>
            </div>
            <div class="eq-header-actions">
                <span class="eq-date-pill">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('d M Y') }}
                </span>
                <a href="{{ route('admin.index') }}" class="eq-btn eq-btn--secondary">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Item Catalog</span>
                </a>
                <button type="button" class="eq-btn eq-btn--secondary" onclick="openDroneModal()">
                    <i class="fa-solid fa-plane-up"></i>
                    <span>+ Add Drone</span>
                </button>
                <button type="button" class="eq-btn eq-btn--primary" onclick="openCameraModal()">
                    <i class="fa-solid fa-camera"></i>
                    <span>+ Add Camera</span>
                </button>
            </div>
        </header>

        @php
            $activeDrones = $drones->where('is_active', true)->count();
            $activeCameras = $cameras->where('is_active', true)->count();
            $totalEquipment = $drones->count() + $cameras->count();
        @endphp

        <!-- KPI Summary Metric Tiles -->
        <section class="eq-kpi-grid" aria-label="Equipment fleet metrics">
            <!-- 1. Registered Drones -->
            <article class="eq-kpi-tile">
                <div class="eq-kpi-icon">
                    <i class="fa-solid fa-plane-up"></i>
                </div>
                <div class="eq-kpi-content">
                    <span class="eq-kpi-label">Aircraft Fleet</span>
                    <span class="eq-kpi-value">{{ $drones->count() }}</span>
                    <span class="eq-kpi-subtext">{{ $activeDrones }} active airframes</span>
                </div>
            </article>

            <!-- 2. Active Airframes -->
            <article class="eq-kpi-tile">
                <div class="eq-kpi-icon eq-kpi-icon--emerald">
                    <i class="fa-solid fa-battery-three-quarters"></i>
                </div>
                <div class="eq-kpi-content">
                    <span class="eq-kpi-label">Flight Ready Drones</span>
                    <span class="eq-kpi-value">{{ $activeDrones }}</span>
                    <span class="eq-kpi-subtext">Calibrated for mission flight</span>
                </div>
            </article>

            <!-- 3. Camera Payloads -->
            <article class="eq-kpi-tile">
                <div class="eq-kpi-icon eq-kpi-icon--teal">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <div class="eq-kpi-content">
                    <span class="eq-kpi-label">Camera Sensors</span>
                    <span class="eq-kpi-value">{{ $cameras->count() }}</span>
                    <span class="eq-kpi-subtext">{{ $activeCameras }} active sensors</span>
                </div>
            </article>

            <!-- 4. Total Operational Payloads -->
            <article class="eq-kpi-tile">
                <div class="eq-kpi-icon eq-kpi-icon--amber">
                    <i class="fa-solid fa-crosshairs"></i>
                </div>
                <div class="eq-kpi-content">
                    <span class="eq-kpi-label">Mission Readiness</span>
                    <span class="eq-kpi-value">{{ $activeDrones + $activeCameras }}</span>
                    <span class="eq-kpi-subtext">Combined active hardware</span>
                </div>
            </article>
        </section>

        <!-- Filter Toolbar -->
        <div class="eq-filter-toolbar">
            <div class="eq-search-box">
                <i class="fa-solid fa-magnifying-glass eq-search-icon"></i>
                <input type="search"
                       id="equipmentSearch"
                       class="eq-search-input"
                       placeholder="Search aircraft model name, camera sensor, or lens..."
                       autocomplete="off">
            </div>

            <div class="eq-select-box">
                <select id="equipmentStatusFilter" class="eq-select-input" aria-label="Filter equipment by status">
                    <option value="">All Statuses</option>
                    <option value="active">Active Equipment</option>
                    <option value="inactive">Inactive Equipment</option>
                </select>
            </div>

            <div class="eq-filter-stats">
                <span id="equipmentResultCount" class="eq-result-badge" aria-live="polite">
                    {{ $totalEquipment }} {{ $totalEquipment === 1 ? 'record' : 'records' }}
                </span>
                <button type="button" id="clearEquipmentFilters" class="eq-clear-btn" title="Reset filters">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- No Results Fallback -->
        <div id="equipmentNoResults" class="eq-register-card" style="display: none;">
            <div class="eq-empty-state">
                <div class="eq-empty-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <h3 class="eq-empty-title">No matching equipment found</h3>
                <p class="eq-empty-text">No aircraft or camera sensors matched your search keywords or status filter.</p>
                <button type="button" onclick="document.getElementById('clearEquipmentFilters')?.click()" class="eq-btn eq-btn--primary" style="margin-top: 0.5rem;">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>

        <div class="equipment-registers">
            <!-- 1. DRONES SECTION -->
            <div class="equipment-register eq-register-card">
                <div class="eq-register-header">
                    <div class="eq-register-title-wrap">
                        <div class="eq-register-icon">
                            <i class="fa-solid fa-plane-up"></i>
                        </div>
                        <h2>Aircraft Fleet (Drones)</h2>
                        <span class="eq-register-count">
                            {{ $drones->count() }} {{ $drones->count() === 1 ? 'aircraft' : 'aircraft' }}
                        </span>
                    </div>
                    <button type="button" class="eq-btn eq-btn--secondary" onclick="openDroneModal()">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Drone</span>
                    </button>
                </div>

                <div class="eq-table-wrap">
                    <table class="eq-table">
                        <thead>
                            <tr>
                                <th>Model Name</th>
                                <th style="width: 220px;">Usable Flight Time</th>
                                <th style="width: 170px;">Status</th>
                                <th class="text-end" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($drones as $drone)
                                <tr data-equipment-row data-status="{{ $drone->is_active ? 'active' : 'inactive' }}">
                                    <td>
                                        <div class="eq-model-name">
                                            <i class="fa-solid fa-plane-up text-slate-400"></i>
                                            <span>{{ $drone->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="eq-spec-pill">
                                            <i class="fa-regular fa-clock"></i>
                                            {{ $drone->usable_flight_time_min }} minutes
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.equipment.drone.toggle', $drone) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" class="eq-status-btn" title="Click to toggle status">
                                                <span class="eq-status-pill {{ $drone->is_active ? 'eq-status-pill--active' : 'eq-status-pill--inactive' }}">
                                                    <span class="eq-status-dot"></span>
                                                    {{ $drone->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <button type="button"
                                                class="eq-action-btn"
                                                data-drone="{{ json_encode($drone) }}"
                                                onclick="openDroneModal(this)"
                                                title="Edit {{ $drone->name }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-0">
                                        <div class="eq-empty-state">
                                            <div class="eq-empty-icon"><i class="fa-solid fa-plane-slash"></i></div>
                                            <h3 class="eq-empty-title">No aircraft configured</h3>
                                            <p class="eq-empty-text">Add your survey aircraft and their operational flight durations to calculate flight mission times.</p>
                                            <button type="button" class="eq-btn eq-btn--primary" onclick="openDroneModal()" style="margin-top: 0.5rem;">
                                                <i class="fa-solid fa-plus"></i>
                                                <span>Register First Drone</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 2. CAMERAS SECTION -->
            <div class="equipment-register eq-register-card">
                <div class="eq-register-header">
                    <div class="eq-register-title-wrap">
                        <div class="eq-register-icon">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                        <h2>Camera & Sensor Payloads</h2>
                        <span class="eq-register-count">
                            {{ $cameras->count() }} {{ $cameras->count() === 1 ? 'camera' : 'cameras' }}
                        </span>
                    </div>
                    <button type="button" class="eq-btn eq-btn--primary" onclick="openCameraModal()">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Camera</span>
                    </button>
                </div>

                <div class="eq-table-wrap">
                    <table class="eq-table">
                        <thead>
                            <tr>
                                <th>Camera Model</th>
                                <th style="width: 170px;">Sensor Size (mm)</th>
                                <th style="width: 140px;">Focal Lens</th>
                                <th style="width: 180px;">Resolution</th>
                                <th style="width: 140px;">Min Interval</th>
                                <th style="width: 150px;">Status</th>
                                <th class="text-end" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cameras as $camera)
                                <tr data-equipment-row data-status="{{ $camera->is_active ? 'active' : 'inactive' }}">
                                    <td>
                                        <div class="eq-model-name">
                                            <i class="fa-solid fa-camera text-slate-400"></i>
                                            <span>{{ $camera->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="eq-spec-pill">
                                            {{ $camera->sensor_width_mm }} × {{ $camera->sensor_height_mm }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="eq-spec-pill">
                                            {{ $camera->focal_length_mm }} mm
                                        </span>
                                    </td>
                                    <td>
                                        <span class="eq-spec-pill">
                                            {{ $camera->image_width_px }} × {{ $camera->image_height_px }} px
                                        </span>
                                    </td>
                                    <td>
                                        <span class="eq-spec-pill">
                                            {{ $camera->min_photo_interval_sec ? $camera->min_photo_interval_sec . 's' : '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.equipment.camera.toggle', $camera) }}" method="POST" class="d-inline m-0">
                                            @csrf
                                            <button type="submit" class="eq-status-btn" title="Click to toggle status">
                                                <span class="eq-status-pill {{ $camera->is_active ? 'eq-status-pill--active' : 'eq-status-pill--inactive' }}">
                                                    <span class="eq-status-dot"></span>
                                                    {{ $camera->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <button type="button"
                                                class="eq-action-btn"
                                                data-camera="{{ json_encode($camera) }}"
                                                onclick="openCameraModal(this)"
                                                title="Edit {{ $camera->name }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-0">
                                        <div class="eq-empty-state">
                                            <div class="eq-empty-icon"><i class="fa-solid fa-camera"></i></div>
                                            <h3 class="eq-empty-title">No cameras configured</h3>
                                            <p class="eq-empty-text">Add photogrammetric cameras and focal sensor specs to calculate ground sampling distance (GSD).</p>
                                            <button type="button" class="eq-btn eq-btn--primary" onclick="openCameraModal()" style="margin-top: 0.5rem;">
                                                <i class="fa-solid fa-plus"></i>
                                                <span>Register First Camera</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Drone Modal -->
    <div class="modal fade eq-modal" id="droneModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="droneForm" method="POST" action="{{ route('admin.equipment.drone.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="droneMethod" value="POST">
                    
                    <div class="modal-header">
                        <h5 class="modal-title" id="droneModalTitle">Add Drone Aircraft</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="eq-form-group">
                            <label class="eq-form-label" for="drone_name">
                                Drone Model Name
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="drone_name" class="eq-form-input" placeholder="e.g. DJI Matrice 350 RTK" required>
                        </div>

                        <div class="eq-form-group">
                            <label class="eq-form-label" for="drone_usable_flight_time_min">
                                Usable Flight Time (minutes)
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="usable_flight_time_min" id="drone_usable_flight_time_min" class="eq-form-input" min="1" placeholder="e.g. 38" required>
                        </div>

                        <label class="eq-switch-wrap mt-3" for="drone_is_active">
                            <input type="checkbox" name="is_active" id="drone_is_active" value="1" checked>
                            <span class="eq-switch-text">Set as Active Flight Equipment</span>
                        </label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="eq-btn eq-btn--secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="eq-btn eq-btn--primary">
                            <i class="fa-solid fa-check"></i>
                            <span>Save Drone</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div class="modal fade eq-modal" id="cameraModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="cameraForm" method="POST" action="{{ route('admin.equipment.camera.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="cameraMethod" value="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="cameraModalTitle">Add Photogrammetric Camera</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="eq-form-group">
                            <label class="eq-form-label" for="camera_name">
                                Camera Model Name
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" id="camera_name" class="eq-form-input" placeholder="e.g. Zenmuse P1 Full-Frame" required>
                        </div>

                        <div class="eq-form-row">
                            <div class="eq-form-group">
                                <label class="eq-form-label" for="camera_sensor_width_mm">
                                    Sensor Width (mm)
                                    <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.0001" name="sensor_width_mm" id="camera_sensor_width_mm" class="eq-form-input" min="0.1" placeholder="35.9" required>
                            </div>
                            <div class="eq-form-group">
                                <label class="eq-form-label" for="camera_sensor_height_mm">
                                    Sensor Height (mm)
                                    <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" step="0.0001" name="sensor_height_mm" id="camera_sensor_height_mm" class="eq-form-input" min="0.1" placeholder="24.0" required>
                            </div>
                        </div>

                        <div class="eq-form-group">
                            <label class="eq-form-label" for="camera_focal_length_mm">
                                Focal Length (mm)
                                <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" step="0.0001" name="focal_length_mm" id="camera_focal_length_mm" class="eq-form-input" min="0.1" placeholder="35.0" required>
                        </div>

                        <div class="eq-form-row">
                            <div class="eq-form-group">
                                <label class="eq-form-label" for="camera_image_width_px">
                                    Image Width (px)
                                    <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="image_width_px" id="camera_image_width_px" class="eq-form-input" min="1" placeholder="8192" required>
                            </div>
                            <div class="eq-form-group">
                                <label class="eq-form-label" for="camera_image_height_px">
                                    Image Height (px)
                                    <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" name="image_height_px" id="camera_image_height_px" class="eq-form-input" min="1" placeholder="5460" required>
                            </div>
                        </div>

                        <div class="eq-form-group">
                            <label class="eq-form-label" for="camera_min_photo_interval_sec">
                                Min Photo Interval (seconds)
                                <span class="text-slate-400 font-normal normal-case">— optional</span>
                            </label>
                            <input type="number" step="0.01" name="min_photo_interval_sec" id="camera_min_photo_interval_sec" class="eq-form-input" min="0.01" placeholder="e.g. 0.7">
                        </div>

                        <label class="eq-switch-wrap mt-2" for="camera_is_active">
                            <input type="checkbox" name="is_active" id="camera_is_active" value="1" checked>
                            <span class="eq-switch-text">Set as Active Photogrammetric Sensor</span>
                        </label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="eq-btn eq-btn--secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="eq-btn eq-btn--primary">
                            <i class="fa-solid fa-check"></i>
                            <span>Save Camera</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const search = document.getElementById('equipmentSearch');
            const status = document.getElementById('equipmentStatusFilter');
            const clearBtn = document.getElementById('clearEquipmentFilters');
            const count = document.getElementById('equipmentResultCount');
            const noResults = document.getElementById('equipmentNoResults');
            const rows = Array.from(document.querySelectorAll('[data-equipment-row]'));

            function filterEquipment() {
                const query = (search?.value || '').trim().toLowerCase();
                const statusValue = status?.value || '';
                let visible = 0;

                rows.forEach(function (row) {
                    const matchesQuery = row.textContent.toLowerCase().includes(query);
                    const matchesStatus = !statusValue || row.dataset.status === statusValue;
                    const show = matchesQuery && matchesStatus;
                    row.hidden = !show;
                    row.style.display = show ? '' : 'none';
                    if (show) visible += 1;
                });

                document.querySelectorAll('.equipment-register').forEach(function (section) {
                    const sectionRows = Array.from(section.querySelectorAll('[data-equipment-row]'));
                    const sectionVisible = sectionRows.some(row => !row.hidden && row.style.display !== 'none');
                    section.hidden = sectionRows.length > 0 && !sectionVisible;
                    section.style.display = (sectionRows.length > 0 && !sectionVisible) ? 'none' : '';
                });

                if (count) count.textContent = visible + (visible === 1 ? ' record' : ' records');
                if (noResults) {
                    noResults.style.display = visible === 0 ? 'block' : 'none';
                    noResults.hidden = visible > 0;
                }
            }

            search?.addEventListener('input', filterEquipment);
            status?.addEventListener('change', filterEquipment);

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    if (search) search.value = '';
                    if (status) status.value = '';
                    filterEquipment();
                });
            }

            // Run initial filter
            filterEquipment();
        });

        function openDroneModal(source = null) {
            const form = document.getElementById('droneForm');
            let drone = null;
            if (source instanceof HTMLElement) {
                try {
                    drone = JSON.parse(source.getAttribute('data-drone') || '{}');
                } catch (e) {
                    console.error('Failed to parse drone JSON:', e);
                }
            } else if (source && typeof source === 'object') {
                drone = source;
            }

            if (drone && drone.id) {
                document.getElementById('droneModalTitle').innerText = 'Edit Drone Aircraft';
                form.action = "{{ url('admin/equipment/drone') }}/" + drone.id;
                document.getElementById('droneMethod').value = 'PUT';
                document.getElementById('drone_name').value = drone.name || '';
                document.getElementById('drone_usable_flight_time_min').value = drone.usable_flight_time_min || '';
                document.getElementById('drone_is_active').checked = Boolean(drone.is_active && drone.is_active !== '0');
            } else {
                document.getElementById('droneModalTitle').innerText = 'Add Drone Aircraft';
                form.action = "{{ route('admin.equipment.drone.store') }}";
                document.getElementById('droneMethod').value = 'POST';
                form.reset();
                document.getElementById('drone_is_active').checked = true;
            }

            const modalEl = document.getElementById('droneModal');
            if (window.bootstrap && window.bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
            }
        }

        function openCameraModal(source = null) {
            const form = document.getElementById('cameraForm');
            let camera = null;
            if (source instanceof HTMLElement) {
                try {
                    camera = JSON.parse(source.getAttribute('data-camera') || '{}');
                } catch (e) {
                    console.error('Failed to parse camera JSON:', e);
                }
            } else if (source && typeof source === 'object') {
                camera = source;
            }

            if (camera && camera.id) {
                document.getElementById('cameraModalTitle').innerText = 'Edit Camera Sensor';
                form.action = "{{ url('admin/equipment/camera') }}/" + camera.id;
                document.getElementById('cameraMethod').value = 'PUT';
                document.getElementById('camera_name').value = camera.name || '';
                document.getElementById('camera_sensor_width_mm').value = camera.sensor_width_mm ?? '';
                document.getElementById('camera_sensor_height_mm').value = camera.sensor_height_mm ?? '';
                document.getElementById('camera_focal_length_mm').value = camera.focal_length_mm ?? '';
                document.getElementById('camera_image_width_px').value = camera.image_width_px ?? '';
                document.getElementById('camera_image_height_px').value = camera.image_height_px ?? '';
                document.getElementById('camera_min_photo_interval_sec').value = camera.min_photo_interval_sec ?? '';
                document.getElementById('camera_is_active').checked = Boolean(camera.is_active && camera.is_active !== '0');
            } else {
                document.getElementById('cameraModalTitle').innerText = 'Add Camera Sensor';
                form.action = "{{ route('admin.equipment.camera.store') }}";
                document.getElementById('cameraMethod').value = 'POST';
                form.reset();
                document.getElementById('camera_is_active').checked = true;
            }

            const modalEl = document.getElementById('cameraModal');
            if (window.bootstrap && window.bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
            }
        }

        // Fallback for modal dismiss buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    if (modal) {
                        if (window.bootstrap && window.bootstrap.Modal) {
                            const inst = bootstrap.Modal.getInstance(modal);
                            if (inst) inst.hide();
                        }
                        modal.classList.remove('show');
                        modal.style.display = 'none';
                        document.querySelectorAll('.modal-backdrop').forEach(function(b) { b.remove(); });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>

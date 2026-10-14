<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Admin - Drone Equipment</x-slot>

    
    <link rel="stylesheet" href="{{ asset('css/drone-equipment.css') }}?v={{ filemtime(public_path('css/drone-equipment.css')) }}">
    
    <div class="page-container equipment-page mt-4">
        <div class="mb-5">
            <div class="equipment-eyebrow">Flight operations</div>
            <h1 class="equipment-title">Drone Mapping Equipment</h1>
            <div class="equipment-subtitle">Manage the aircraft and cameras available for mapping missions.</div>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-0 border-0 bg-light text-success mb-4" style="border-left: 3px solid #10b981 !important;">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger rounded-0 border-0 bg-light text-danger mb-4" style="border-left: 3px solid #ef4444 !important;">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="equipment-registers">
            <!-- DRONES SECTION -->
            <div class="equipment-register mb-5">
                <div class="custom-card">
                    <div class="custom-card-header">
                        <h4 class="custom-card-title"><i class="fa-solid fa-plane-up me-2" style="color:#888;"></i> Drones</h4>
                        <button type="button" class="btn-outline-dark-minimal" onclick="openDroneModal()">+ Add Drone</button>
                    </div>
                    <div class="table-responsive">
                        <table class="clean-table">
                            <thead>
                                <tr>
                                    <th>Model Name</th>
                                    <th>Usable Time</th>
                                    <th>Status</th>
                                    <th class="text-end">Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($drones as $drone)
                                <tr>
                                    <td class="fw-medium">{{ $drone->name }}</td>
                                    <td>{{ $drone->usable_flight_time_min }} min</td>
                                    <td>
                                        <form action="{{ route('admin.equipment.drone.toggle', $drone) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" style="background:none; border:none; padding:0;">
                                                <span class="status-badge {{ $drone->is_active ? 'status-active' : 'status-inactive' }}">
                                                    {{ $drone->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="action-btn" data-drone="{{ json_encode($drone) }}" onclick="openDroneModal(this)" title="Edit {{ $drone->name }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No drones configured.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CAMERAS SECTION -->
            <div class="equipment-register mb-5">
                <div class="custom-card">
                    <div class="custom-card-header">
                        <h4 class="custom-card-title"><i class="fa-solid fa-camera me-2" style="color:#888;"></i> Cameras</h4>
                        <button type="button" class="btn-outline-dark-minimal" onclick="openCameraModal()">+ Add Camera</button>
                    </div>
                    <div class="table-responsive">
                        <table class="clean-table">
                            <thead>
                                <tr>
                                    <th>Model Name</th>
                                    <th>Sensor (mm)</th>
                                    <th>Lens</th>
                                    <th>Res (px)</th>
                                    <th>Min Int.</th>
                                    <th>Status</th>
                                    <th class="text-end">Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cameras as $camera)
                                <tr>
                                    <td class="fw-medium">{{ $camera->name }}</td>
                                    <td>{{ $camera->sensor_width_mm }}x{{ $camera->sensor_height_mm }}</td>
                                    <td>{{ $camera->focal_length_mm }}mm</td>
                                    <td>{{ $camera->image_width_px }}x{{ $camera->image_height_px }}</td>
                                    <td>{{ $camera->min_photo_interval_sec ? $camera->min_photo_interval_sec . 's' : '-' }}</td>
                                    <td>
                                        <form action="{{ route('admin.equipment.camera.toggle', $camera) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" style="background:none; border:none; padding:0;">
                                                <span class="status-badge {{ $camera->is_active ? 'status-active' : 'status-inactive' }}">
                                                    {{ $camera->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="action-btn" data-camera="{{ json_encode($camera) }}" onclick="openCameraModal(this)" title="Edit {{ $camera->name }}">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No cameras configured.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drone Modal -->
    <div class="modal fade" id="droneModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="droneForm" method="POST" action="{{ route('admin.equipment.drone.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="droneMethod" value="POST">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-bold" id="droneModalTitle" style="font-size: 1.1rem;">Add Drone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="input-label">Drone Name</label>
                            <input type="text" name="name" id="drone_name" class="minimal-input" required>
                        </div>
                        <div class="mb-4">
                            <label class="input-label">Usable Flight Time (minutes)</label>
                            <input type="number" name="usable_flight_time_min" id="drone_usable_flight_time_min" class="minimal-input" min="1" required>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="drone_is_active" value="1" checked style="cursor: pointer;">
                            <label class="form-check-label ms-1" style="font-weight: 500; font-size: 0.9rem;">Set as Active Equipment</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline-dark-minimal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-dark-minimal">Save Drone</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div class="modal fade" id="cameraModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="cameraForm" method="POST" action="{{ route('admin.equipment.camera.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="cameraMethod" value="POST">
                    <div class="modal-header d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-bold" id="cameraModalTitle" style="font-size: 1.1rem;">Add Camera</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="input-label">Camera Name</label>
                            <input type="text" name="name" id="camera_name" class="minimal-input" required>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="input-label">Sensor Width (mm)</label>
                                <input type="number" step="0.0001" name="sensor_width_mm" id="camera_sensor_width_mm" class="minimal-input" min="0.1" required>
                            </div>
                            <div class="col-6">
                                <label class="input-label">Sensor Height (mm)</label>
                                <input type="number" step="0.0001" name="sensor_height_mm" id="camera_sensor_height_mm" class="minimal-input" min="0.1" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="input-label">Focal Length (mm)</label>
                            <input type="number" step="0.0001" name="focal_length_mm" id="camera_focal_length_mm" class="minimal-input" min="0.1" required>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="input-label">Image Width (px)</label>
                                <input type="number" name="image_width_px" id="camera_image_width_px" class="minimal-input" min="1" required>
                            </div>
                            <div class="col-6">
                                <label class="input-label">Image Height (px)</label>
                                <input type="number" name="image_height_px" id="camera_image_height_px" class="minimal-input" min="1" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="input-label">Min Photo Interval (sec) <span class="text-lowercase fw-normal text-muted">— optional</span></label>
                            <input type="number" step="0.01" name="min_photo_interval_sec" id="camera_min_photo_interval_sec" class="minimal-input" min="0.01" placeholder="Leave empty if unknown">
                        </div>
                        
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" id="camera_is_active" value="1" checked style="cursor: pointer;">
                            <label class="form-check-label ms-1" style="font-weight: 500; font-size: 0.9rem;">Set as Active Equipment</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-outline-dark-minimal" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-dark-minimal">Save Camera</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
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
                document.getElementById('droneModalTitle').innerText = 'Edit Drone';
                form.action = "{{ url('admin/equipment/drone') }}/" + drone.id;
                document.getElementById('droneMethod').value = 'PUT';
                document.getElementById('drone_name').value = drone.name || '';
                document.getElementById('drone_usable_flight_time_min').value = drone.usable_flight_time_min || '';
                document.getElementById('drone_is_active').checked = Boolean(drone.is_active && drone.is_active !== '0');
            } else {
                document.getElementById('droneModalTitle').innerText = 'Add Drone';
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
                document.getElementById('cameraModalTitle').innerText = 'Edit Camera';
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
                document.getElementById('cameraModalTitle').innerText = 'Add Camera';
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

        // Fallback for modal dismiss buttons if Bootstrap JS handler is not caught
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
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(function(b) { b.remove(); });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>

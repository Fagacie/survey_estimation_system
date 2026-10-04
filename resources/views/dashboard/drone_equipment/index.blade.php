<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Admin - Drone Equipment</x-slot>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        .page-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #888;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .custom-card {
            background: #fff;
            border: 1px solid #eaeaea;
            border-radius: 6px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .custom-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #eaeaea;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
        }

        .custom-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #111;
            margin: 0;
            letter-spacing: -0.02em;
        }

        /* BUTTONS */
        .btn-outline-dark-minimal {
            background: transparent;
            border: 1px solid #111;
            color: #111;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-outline-dark-minimal:hover {
            background: #111;
            color: #fff;
        }
        
        .btn-dark-minimal {
            background: #111;
            border: 1px solid #111;
            color: #fff;
            padding: 0.6rem 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.2s;
            display: inline-block;
            text-decoration: none;
        }
        .btn-dark-minimal:hover {
            background: #333;
            border-color: #333;
            color: #fff;
        }

        /* TABLES */
        .clean-table {
            width: 100%;
            margin-bottom: 0;
        }
        .clean-table th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #888;
            font-weight: 600;
            padding: 1rem 1.5rem;
            background: #fff;
            border-bottom: 1px solid #eaeaea;
        }
        .clean-table td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            color: #444;
            font-size: 0.9rem;
            border-bottom: 1px solid #eaeaea;
        }
        .clean-table tbody tr:last-child td {
            border-bottom: none;
        }
        .clean-table tbody tr:hover {
            background-color: #fcfcfc;
        }

        /* BADGES */
        .status-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.35em 0.8em;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .status-inactive {
            background: rgba(107, 114, 128, 0.1);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, 0.2);
        }

        /* ACTION BUTTONS */
        .action-btn {
            background: none;
            border: none;
            color: #888;
            transition: color 0.2s;
            padding: 0.2rem 0.5rem;
        }
        .action-btn:hover {
            color: #111;
        }

        /* MODALS */
        .minimal-input {
            border: 1px solid #eaeaea;
            border-radius: 4px;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            background: #fafafa;
            color: #111;
            width: 100%;
            transition: border-color 0.2s;
        }
        .minimal-input:focus {
            outline: none;
            border-color: #111;
            background: #fff;
        }
        .input-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #888;
            margin-bottom: 0.4rem;
            display: block;
            font-weight: 600;
        }
        
        .modal-content {
            border: none;
            border-radius: 8px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .modal-header {
            border-bottom: 1px solid #eaeaea;
            padding: 1.5rem;
        }
        .modal-body {
            padding: 1.5rem;
        }
        .modal-footer {
            border-top: 1px solid #eaeaea;
            padding: 1.5rem;
            background: #fafafa;
        }
    </style>
    
    <div class="page-container mt-4">
        <div class="mb-5">
            <h1 class="display-5 fw-bold mb-2" style="color: #111; letter-spacing: -0.03em;">Equipment Database</h1>
            <div class="text-muted" style="font-size: 0.95rem;">Manage Drones and Cameras for Drone Mapping Operations</div>
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

        <div class="row">
            <!-- DRONES SECTION -->
            <div class="col-lg-5 mb-5">
                <div class="custom-card">
                    <div class="custom-card-header">
                        <h4 class="custom-card-title"><i class="fa-solid fa-plane-up me-2" style="color:#888;"></i> Drones</h4>
                        <button class="btn-outline-dark-minimal" data-bs-toggle="modal" data-bs-target="#droneModal" onclick="openDroneModal()">+ Add Drone</button>
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
                                        <button class="action-btn" onclick="openDroneModal({{ $drone->toJson() }})"><i class="fa-solid fa-pen"></i></button>
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
            <div class="col-lg-7 mb-5">
                <div class="custom-card">
                    <div class="custom-card-header">
                        <h4 class="custom-card-title"><i class="fa-solid fa-camera me-2" style="color:#888;"></i> Cameras</h4>
                        <button class="btn-outline-dark-minimal" data-bs-toggle="modal" data-bs-target="#cameraModal" onclick="openCameraModal()">+ Add Camera</button>
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
                                        <button class="action-btn" onclick="openCameraModal({{ $camera->toJson() }})"><i class="fa-solid fa-pen"></i></button>
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
                            <input type="number" step="0.01" name="min_photo_interval_sec" id="camera_min_photo_interval_sec" class="minimal-input" min="0.1" placeholder="Leave empty if unknown">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openDroneModal(drone = null) {
            const form = document.getElementById('droneForm');
            if (drone) {
                document.getElementById('droneModalTitle').innerText = 'Edit Drone';
                form.action = `/admin/equipment/drone/${drone.id}`;
                document.getElementById('droneMethod').value = 'PUT';
                document.getElementById('drone_name').value = drone.name;
                document.getElementById('drone_usable_flight_time_min').value = drone.usable_flight_time_min;
                document.getElementById('drone_is_active').checked = drone.is_active;
            } else {
                document.getElementById('droneModalTitle').innerText = 'Add Drone';
                form.action = `/admin/equipment/drone`;
                document.getElementById('droneMethod').value = 'POST';
                form.reset();
                document.getElementById('drone_is_active').checked = true;
            }
        }

        function openCameraModal(camera = null) {
            const form = document.getElementById('cameraForm');
            if (camera) {
                document.getElementById('cameraModalTitle').innerText = 'Edit Camera';
                form.action = `/admin/equipment/camera/${camera.id}`;
                document.getElementById('cameraMethod').value = 'PUT';
                document.getElementById('camera_name').value = camera.name;
                document.getElementById('camera_sensor_width_mm').value = camera.sensor_width_mm;
                document.getElementById('camera_sensor_height_mm').value = camera.sensor_height_mm;
                document.getElementById('camera_focal_length_mm').value = camera.focal_length_mm;
                document.getElementById('camera_image_width_px').value = camera.image_width_px;
                document.getElementById('camera_image_height_px').value = camera.image_height_px;
                document.getElementById('camera_min_photo_interval_sec').value = camera.min_photo_interval_sec || '';
                document.getElementById('camera_is_active').checked = camera.is_active;
            } else {
                document.getElementById('cameraModalTitle').innerText = 'Add Camera';
                form.action = `/admin/equipment/camera`;
                document.getElementById('cameraMethod').value = 'POST';
                form.reset();
                document.getElementById('camera_is_active').checked = true;
            }
        }
    </script>
    @endpush
</x-app-layout>

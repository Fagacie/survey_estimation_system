// public/js/drone_mapping/ui.js

const DroneUI = {
    init: function(containerId) {
        this.container = document.getElementById(containerId);
        if (!this.container) return;

        this.renderForm();
        this.bindEvents();
        this.updateCalculations();
    },

    renderForm: function() {
        let cameraOptions = '';
        for (let key in window.DRONE_CAMERAS) {
            cameraOptions += `<option value="${key}">${window.DRONE_CAMERAS[key].name}</option>`;
        }

        const html = `
            <div class="accordion-body">
                <!-- Camera Selection -->
                <div class="section-label mb-2" style="color: var(--accent-green);"><i class="fa-solid fa-camera"></i> Camera Configuration</div>
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <select id="drone_camera_model" name="drone_camera_model" class="form-select form-control-panel">
                            ${cameraOptions}
                        </select>
                    </div>
                </div>

                <!-- Flight Parameters -->
                <div class="section-label mb-2" style="color: var(--accent-blue);"><i class="fa-solid fa-sliders"></i> Flight Parameters</div>
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label-panel">Altitude (m)</label>
                        <input type="number" id="drone_altitude" name="drone_altitude" min="10" max="500" step="1" value="100" class="form-control-panel w-100">
                    </div>
                    
                    <div class="col-6">
                        <label class="form-label-panel">Speed (m/s)</label>
                        <input type="number" id="drone_speed" name="drone_speed" min="1" max="25" step="0.5" value="15" class="form-control-panel w-100">
                    </div>
                    
                    <div class="col-6 mt-2">
                        <label class="form-label-panel">Front Overlap (%)</label>
                        <input type="number" id="drone_front_overlap" name="drone_front_overlap" min="10" max="90" step="1" value="80" class="form-control-panel w-100">
                    </div>

                    <div class="col-6 mt-2">
                        <label class="form-label-panel">Side Overlap (%)</label>
                        <input type="number" id="drone_side_overlap" name="drone_side_overlap" min="10" max="90" step="1" value="70" class="form-control-panel w-100">
                    </div>

                    <div class="col-12 mt-2">
                        <label class="form-label-panel">Course Angle (°)</label>
                        <input type="number" id="drone_course_angle" name="drone_course_angle" min="0" max="359" step="1" value="0" class="form-control-panel w-100">
                    </div>
                </div>

                <!-- Actions -->
                <button type="button" id="btn_generate_drone_lines" class="btn btn-ws btn-ws-primary w-100 mb-2">
                    <i class="fa-solid fa-plane-departure"></i> Generate Flight Path
                </button>

                <div id="drone-stale-warning" class="stale-warning d-none mb-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Parameters changed. Regenerate flight path.
                </div>

                <button id="btn_clear_drone_lines" class="btn btn-ws btn-ws-outline-danger w-100 mb-4">
                    <i class="fa-solid fa-eraser"></i> Clear Map Lines
                </button>

                <!-- Estimated Results -->
                <div class="section-label mb-2" style="color: var(--accent-purple);"><i class="fa-solid fa-chart-simple"></i> Flight Statistics</div>
                
                <div class="stat-row">
                    <span class="stat-label">Survey Area</span>
                    <span id="res_area" class="stat-value">0.00 m²</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Boundary Perimeter</span>
                    <span id="res_perimeter" class="stat-value">0.00 m</span>
                </div>
                
                <div class="stat-row mt-2 pt-2" style="border-top: 1px dashed var(--sb-border);">
                    <span class="stat-label">Ortho GSD</span>
                    <span id="res_gsd" class="stat-value">0.00 cm/px</span>
                </div>
                
                <div class="stat-row">
                    <span class="stat-label">Total Flight Distance</span>
                    <span id="res_distance" class="stat-value">0 m</span>
                    <input type="hidden" id="drone_total_distance" name="drone_total_distance" value="0">
                </div>
                
                <div class="stat-row">
                    <span class="stat-label">Total Images</span>
                    <span id="res_images" class="stat-value">0</span>
                    <input type="hidden" id="drone_total_images" name="drone_total_images" value="0">
                </div>

                <div class="stat-row">
                    <span class="stat-label">Est. Batteries (Pairs)</span>
                    <span id="res_batteries" class="stat-value">0</span>
                </div>

                <div class="stat-row mt-2 pt-2" style="border-top: 1px solid var(--sb-border);">
                    <span class="stat-label fw-bold">EST. FLIGHT DURATION</span>
                    <span id="res_duration" class="stat-value highlight" style="color: var(--accent-green); font-size: 1.05rem;">00h 00m</span>
                    <input type="hidden" id="drone_duration_hours" name="drone_duration_hours" value="0">
                </div>
            </div>
        `;
        this.container.innerHTML = html;
    },

    bindEvents: function() {
        const _this = this;
        const inputs = ['drone_altitude', 'drone_speed', 'drone_front_overlap', 'drone_side_overlap', 'drone_course_angle'];
        
        inputs.forEach(id => {
            const el = document.getElementById(id);
            if(el) {
                el.addEventListener('input', function() {
                    _this.updateCalculations();
                    
                    // Mark as stale if geometry affects lines
                    if (window.drawnItems && window.drawnItems.getLayers().length > 0) {
                        const warning = document.getElementById('drone-stale-warning');
                        if (warning) warning.classList.remove('d-none');
                    }
                });
            }
        });

        const camSelect = document.getElementById('drone_camera_model');
        if (camSelect) {
            camSelect.addEventListener('change', () => _this.updateCalculations());
        }

        const btnGen = document.getElementById('btn_generate_drone_lines');
        if (btnGen) {
            btnGen.addEventListener('click', () => {
                // Call global generation if it exists on window
                if (window.requestDroneLinesGeneration) {
                    window.requestDroneLinesGeneration();
                }
            });
        }
    },

    updateCalculations: function() {
        const camKey = document.getElementById('drone_camera_model').value;
        const altitude = parseFloat(document.getElementById('drone_altitude').value);
        const speed = parseFloat(document.getElementById('drone_speed').value);
        const frontOverlap = parseFloat(document.getElementById('drone_front_overlap').value);
        const sideOverlap = parseFloat(document.getElementById('drone_side_overlap').value);
        
        const cameraSpec = window.DRONE_CAMERAS[camKey];
        if (!cameraSpec || !window.PhotogrammetryMath) return;

        // 1. Calculate GSD
        const gsd = window.PhotogrammetryMath.calculateGSD(altitude, cameraSpec);
        document.getElementById('res_gsd').innerText = gsd.toFixed(2) + ' cm/pixel';

        // 2. We can pre-calculate ground spacing, but we need Turf.js (via Worker) to get exact distance.
        // For now, if we have a current Polygon area in window.currentSurveyArea, we can do an estimation.
        // But actual numbers will be updated when the Worker returns the actual lines.
        
        this.currentSettings = {
            cameraSpec,
            altitude,
            speed,
            frontOverlap,
            sideOverlap,
            gsd
        };
    },

    updateFinalMetrics: function(totalDistanceMeters) {
        if (!this.currentSettings || !window.PhotogrammetryMath) return;
        
        const { cameraSpec, altitude, speed, frontOverlap } = this.currentSettings;

        // Add 15% buffer for turnarounds on edges if we just use raw clipped lines
        totalDistanceMeters = totalDistanceMeters * 1.15;

        // 1. Calculate distance between shots
        const footprints = window.PhotogrammetryMath.calculateGroundFootprint(altitude, cameraSpec);
        const shotSpacing = window.PhotogrammetryMath.calculateShotSpacing(footprints.ground_height_m, frontOverlap);

        // 2. Total Images = (Total Distance / Shot Spacing)
        const totalImages = Math.ceil(totalDistanceMeters / shotSpacing);

        // 3. Duration = Total Distance / Speed
        const durationSeconds = totalDistanceMeters / speed;
        
        const hours = Math.floor(durationSeconds / 3600);
        const minutes = Math.floor((durationSeconds % 3600) / 60);
        const seconds = Math.floor(durationSeconds % 60);

        // 4. Batteries (Assume 30 mins effective flight time per battery pair)
        const durationMinutes = durationSeconds / 60;
        const batteryPairs = Math.ceil(durationMinutes / 30);

        // Update UI
        document.getElementById('res_distance').innerText = Math.round(totalDistanceMeters).toLocaleString() + ' m';
        document.getElementById('drone_total_distance').value = totalDistanceMeters;
        
        document.getElementById('res_duration').innerText = `${hours}h ${minutes}m ${seconds}s`;
        document.getElementById('drone_duration_hours').value = durationSeconds / 3600;
        
        document.getElementById('res_images').innerText = totalImages.toLocaleString() + ' images';
        document.getElementById('drone_total_images').value = totalImages;

        const batteryEl = document.getElementById('res_batteries');
        if (batteryEl) batteryEl.innerText = batteryPairs.toLocaleString();
    }
};

if (typeof window !== 'undefined') {
    window.DroneUI = DroneUI;
}

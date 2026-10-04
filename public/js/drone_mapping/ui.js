// public/js/drone_mapping/ui.js

const DroneUI = {
    init: function(containerId, savedParams = null) {
        this.container = document.getElementById(containerId);
        if (!this.container) return;

        this.renderForm();
        
        if (savedParams) {
            this.loadSavedParams(savedParams);
        }
        
        this.bindEvents();
        this.updateCalculations();
    },

    loadSavedParams: function(params) {
        const setVal = (id, val) => {
            const el = document.getElementById(id);
            if (el && val !== null && val !== undefined) el.value = val;
        };
        
        setVal('drone_model_select', params.drone_model);
        setVal('drone_camera_model', params.camera_model);
        setVal('drone_altitude', params.altitude_m);
        setVal('drone_target_gsd', params.target_gsd_cm);
        setVal('drone_front_overlap', params.front_overlap_percent);
        setVal('drone_side_overlap', params.side_overlap_percent);
        setVal('drone_speed', params.speed_ms);
        setVal('drone_mapping_margin', params.mapping_margin_m);
        setVal('drone_course_angle', params.course_angle_deg);
        
        if (params.target_gsd_cm > 0 && params.altitude_m == null) {
            const gsdMode = document.getElementById('mode_gsd');
            if (gsdMode) {
                gsdMode.checked = true;
                const altInput = document.getElementById('drone_altitude');
                const gsdInput = document.getElementById('drone_target_gsd');
                if (altInput) altInput.disabled = true;
                if (gsdInput) gsdInput.disabled = false;
            }
        }

        if (params.capture_mode) {
            const captureModeEl = document.getElementById(`capture_mode_${params.capture_mode}`);
            if (captureModeEl) captureModeEl.checked = true;
        }

        if (params.course_angle_deg !== null && params.course_angle_deg !== undefined && params.course_angle_deg > 0) {
            const angleManual = document.getElementById('angle_mode_manual');
            if (angleManual) angleManual.checked = true;
            const angleInput = document.getElementById('drone_course_angle');
            if (angleInput) angleInput.disabled = false;
        }
    },

    renderForm: function() {
        let cameraOptions = '';
        if (window.DB_CAMERAS) {
            window.DB_CAMERAS.forEach(cam => {
                cameraOptions += `<option value="${cam.name}">${cam.name}</option>`;
            });
        }
        
        let droneOptions = '';
        if (window.DB_DRONES) {
            window.DB_DRONES.forEach(drone => {
                droneOptions += `<option value="${drone.name}">${drone.name}</option>`;
            });
        }

        const html = `
            <div class="accordion-body" style="padding: 12px;">
                <!-- SECTION 1: Survey Area -->
                <div class="section-label mb-2" style="color: var(--accent-blue); border-bottom: 1px solid var(--sb-border); padding-bottom: 5px; font-weight: 600;">
                    <i class="fa-solid fa-draw-polygon"></i> Survey Area
                </div>
                <div class="mb-3" style="font-size: 0.85rem; color: var(--bs-secondary); padding: 0 4px;">
                    Use the map drawing tools on the right to define the survey boundary polygon.
                </div>

                <!-- SECTION 2: Equipment -->
                <div class="section-label mb-2" style="color: var(--accent-green); border-bottom: 1px solid var(--sb-border); padding-bottom: 5px; font-weight: 600;">
                    <i class="fa-solid fa-plane"></i> Equipment
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <label class="form-label-panel">Drone</label>
                        <select id="drone_model_select" name="drone_model" class="form-select form-control-panel">
                            ${droneOptions}
                        </select>
                    </div>
                    <div class="col-12 mt-2">
                        <label class="form-label-panel">Camera</label>
                        <select id="drone_camera_model" name="drone_camera_model" class="form-select form-control-panel">
                            ${cameraOptions}
                        </select>
                    </div>
                </div>

                <!-- SECTION 3: Mapping Parameters -->
                <div class="section-label mb-2" style="color: var(--accent-purple); border-bottom: 1px solid var(--sb-border); padding-bottom: 5px; font-weight: 600;">
                    <i class="fa-solid fa-sliders"></i> Mapping Parameters
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-12 mb-2">
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="calc_mode" id="mode_altitude" value="altitude" checked>
                            <label class="btn btn-outline-primary btn-sm" for="mode_altitude">Altitude Mode</label>
                            
                            <input type="radio" class="btn-check" name="calc_mode" id="mode_gsd" value="gsd">
                            <label class="btn btn-outline-primary btn-sm" for="mode_gsd">Target GSD Mode</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <label class="form-label-panel">Altitude (m)</label>
                        <input type="number" id="drone_altitude" name="drone_altitude" min="10" max="500" step="1" value="100" class="form-control-panel w-100">
                    </div>
                    <div class="col-6">
                        <label class="form-label-panel">Target GSD (cm/px)</label>
                        <input type="number" id="drone_target_gsd" name="drone_target_gsd" min="0.1" max="20" step="0.1" value="" class="form-control-panel w-100" disabled>
                    </div>
                    
                    <div class="col-6 mt-2">
                        <label class="form-label-panel">Front Overlap (%)</label>
                        <input type="number" id="drone_front_overlap" name="drone_front_overlap" min="10" max="95" step="1" value="80" class="form-control-panel w-100">
                    </div>
                    <div class="col-6 mt-2">
                        <label class="form-label-panel">Side Overlap (%)</label>
                        <input type="number" id="drone_side_overlap" name="drone_side_overlap" min="10" max="95" step="1" value="70" class="form-control-panel w-100">
                    </div>
                    
                    <div class="col-6 mt-2">
                        <label class="form-label-panel">Flight Speed (m/s)</label>
                        <input type="number" id="drone_speed" name="drone_speed" min="1" max="25" step="0.5" value="10" class="form-control-panel w-100">
                    </div>
                    <div class="col-6 mt-2">
                        <label class="form-label-panel" title="Inward distance from the survey boundary.">Mapping Margin (m)</label>
                        <input type="number" id="drone_mapping_margin" name="drone_mapping_margin" min="0" max="500" step="1" value="0" class="form-control-panel w-100">
                    </div>
                </div>

                <!-- SECTION 4: Flight Direction -->
                <div class="section-label mb-2" style="color: var(--accent-amber); border-bottom: 1px solid var(--sb-border); padding-bottom: 5px; font-weight: 600;">
                    <i class="fa-solid fa-compass"></i> Flight Direction
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2" style="padding-left: 4px;">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="angle_mode" id="angle_mode_auto" value="auto" checked>
                                <label class="form-check-label" for="angle_mode_auto">Auto</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="angle_mode" id="angle_mode_manual" value="manual">
                                <label class="form-check-label" for="angle_mode_manual">Manual</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label-panel">Course Angle (°)</label>
                        <input type="number" id="drone_course_angle" name="drone_course_angle" min="0" max="359" step="1" value="0" class="form-control-panel w-100" disabled>
                    </div>
                </div>

                <!-- SECTION 5: Capture -->
                <div class="section-label mb-2" style="color: var(--bs-teal); border-bottom: 1px solid var(--sb-border); padding-bottom: 5px; font-weight: 600;">
                    <i class="fa-solid fa-camera-retro"></i> Capture
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-12 mb-2">
                        <label class="form-label-panel">Capture Mode</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="capture_mode" id="capture_mode_timed" value="timed" checked>
                            <label class="btn btn-outline-secondary btn-sm" for="capture_mode_timed">Timed</label>
                            
                            <input type="radio" class="btn-check" name="capture_mode" id="capture_mode_distance" value="distance">
                            <label class="btn btn-outline-secondary btn-sm" for="capture_mode_distance">Distance</label>
                        </div>
                    </div>
                    
                    <div class="col-6 mt-2">
                        <label class="form-label-panel">Photo Spacing</label>
                        <div id="res_photo_spacing" class="form-control-panel bg-light text-end" style="height: auto; padding: 4px 8px;">0.0 m</div>
                        <input type="hidden" id="drone_photo_spacing_val" name="drone_photo_spacing_val" value="0">
                    </div>
                    <div class="col-6 mt-2">
                        <label class="form-label-panel">Photo Interval</label>
                        <div id="res_photo_interval" class="form-control-panel bg-light text-end" style="height: auto; padding: 4px 8px;">0.0 s</div>
                        <input type="hidden" id="drone_photo_interval_val" name="drone_photo_interval_val" value="0">
                    </div>
                </div>

                <!-- Validation Warnings -->
                <div id="drone-mission-validation-warning" class="stale-warning d-none mb-2" style="background-color: var(--bs-danger-bg-subtle); color: var(--bs-danger-text-emphasis);">
                    <i class="fa-solid fa-circle-xmark"></i> <span id="mission-validation-text"></span>
                </div>
                <div id="drone-interval-warning" class="stale-warning d-none mb-2" style="background-color: var(--bs-warning-bg-subtle); color: var(--bs-warning-text-emphasis);">
                    <i class="fa-solid fa-camera"></i> <span id="interval-warning-text"></span>
                </div>
                <div id="drone-stale-warning" class="stale-warning d-none mb-3">
                    <i class="fa-solid fa-triangle-exclamation"></i> Parameters changed. Regenerate flight path.
                </div>

                <!-- GENERATE ACTION -->
                <button type="button" id="btn_generate_drone_lines" class="btn btn-ws btn-ws-primary w-100 mb-2" style="font-size: 1rem; padding: 10px;">
                    <i class="fa-solid fa-plane-departure"></i> Generate Mapping Route
                </button>
                <button id="btn_clear_drone_lines" class="btn btn-ws btn-ws-outline-danger w-100 mb-4">
                    <i class="fa-solid fa-eraser"></i> Clear Map Lines
                </button>

                <!-- SECTION 6: Mission Estimate -->
                <div class="section-label mb-2" style="color: var(--accent-orange); border-bottom: 1px solid var(--sb-border); padding-bottom: 5px; font-weight: 600;">
                    <i class="fa-solid fa-chart-simple"></i> Mission Estimate
                </div>
                
                <div class="stat-row">
                    <span class="stat-label">Flight Lines</span>
                    <span id="res_flight_lines" class="stat-value">0</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Total Images</span>
                    <span id="res_images" class="stat-value">0</span>
                    <input type="hidden" id="drone_total_images" name="drone_total_images" value="0">
                </div>
                
                <div class="stat-row mt-2 pt-2" style="border-top: 1px dashed var(--sb-border);">
                    <span class="stat-label">Flight Distance</span>
                    <span id="res_distance" class="stat-value">0 m</span>
                    <input type="hidden" id="drone_total_distance" name="drone_total_distance" value="0">
                </div>
                <div class="stat-row">
                    <span class="stat-label">Pure Flight Time</span>
                    <span id="res_duration" class="stat-value" style="color: var(--accent-green); font-weight: bold;">00h 00m</span>
                    <input type="hidden" id="drone_duration_hours" name="drone_duration_hours" value="0">
                </div>
                
                <div class="stat-row mt-2 pt-2" style="border-top: 1px dashed var(--sb-border);">
                    <span class="stat-label">Estimated Sorties<br><span id="res_usable_time" style="font-size: 0.75rem; font-weight: normal; color: var(--bs-secondary);"></span></span>
                    <span id="res_sorties" class="stat-value highlight" style="color: var(--accent-purple);">0</span>
                    <input type="hidden" id="drone_sortie_count" name="drone_sortie_count" value="0">
                    <input type="hidden" id="drone_usable_time_val" name="drone_usable_time_val" value="0">
                </div>

                <!-- HIDDEN DATA (For validation, saving, and background math) -->
                <div class="d-none">
                    <span id="res_area">0.00 m²</span>
                    <span id="res_gsd">0.00 cm/px</span>
                    <span id="res_line_spacing">0.0 m</span>
                    <input type="hidden" id="drone_gsd_val" name="drone_gsd_val" value="0">
                    <input type="hidden" id="drone_ground_width_val" name="drone_ground_width_val" value="0">
                    <input type="hidden" id="drone_ground_height_val" name="drone_ground_height_val" value="0">
                    <input type="hidden" id="drone_line_spacing_val" name="drone_line_spacing_val" value="0">
                </div>
            </div>
        `;
        this.container.innerHTML = html;
    },

    bindEvents: function() {
        const _this = this;
        
        // Mode toggle logic
        document.querySelectorAll('input[name="calc_mode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const altInput = document.getElementById('drone_altitude');
                const gsdInput = document.getElementById('drone_target_gsd');
                if (this.value === 'altitude') {
                    altInput.disabled = false;
                    gsdInput.disabled = true;
                } else {
                    altInput.disabled = true;
                    gsdInput.disabled = false;
                    // Trigger a calculation using current GSD
                    _this.updateCalculations();
                }
            });
        });

        // Angle mode logic
        document.querySelectorAll('input[name="angle_mode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const angleInput = document.getElementById('drone_course_angle');
                if (this.value === 'auto') {
                    angleInput.disabled = true;
                } else {
                    angleInput.disabled = false;
                }
                
                if (window.drawnItems && window.drawnItems.getLayers().length > 0) {
                    const warning = document.getElementById('drone-stale-warning');
                    if (warning) warning.classList.remove('d-none');
                }
            });
        });

        // Capture mode logic
        document.querySelectorAll('input[name="capture_mode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                _this.updateCalculations();
            });
        });

        const inputs = ['drone_altitude', 'drone_target_gsd', 'drone_speed', 'drone_front_overlap', 'drone_side_overlap', 'drone_course_angle', 'drone_mapping_margin'];
        
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
        
        const droneSelect = document.getElementById('drone_model_select');
        if (droneSelect) {
            droneSelect.addEventListener('change', () => _this.updateCalculations()); // Might affect sortie count without generating lines
        }

        const btnGen = document.getElementById('btn_generate_drone_lines');
        if (btnGen) {
            btnGen.addEventListener('click', () => {
                if (window.requestDroneLinesGeneration) {
                    window.requestDroneLinesGeneration();
                }
            });
        }
    },

    updateCalculations: function() {
        const droneName = document.getElementById('drone_model_select').value;
        const camName = document.getElementById('drone_camera_model').value;
        const mode = document.querySelector('input[name="calc_mode"]:checked').value;
        const captureMode = document.querySelector('input[name="capture_mode"]:checked').value;
        
        const altInput = document.getElementById('drone_altitude');
        const gsdInput = document.getElementById('drone_target_gsd');
        const speed = parseFloat(document.getElementById('drone_speed').value) || 10;
        const frontOverlap = parseFloat(document.getElementById('drone_front_overlap').value) || 80;
        const sideOverlap = parseFloat(document.getElementById('drone_side_overlap').value) || 70;
        const margin = parseFloat(document.getElementById('drone_mapping_margin').value) || 0;
        
        const droneSpec = window.DB_DRONES ? window.DB_DRONES.find(d => d.name === droneName) : null;
        const cameraSpec = window.DB_CAMERAS ? window.DB_CAMERAS.find(c => c.name === camName) : null;
        
        if (!cameraSpec || !droneSpec || !window.PhotogrammetryMath) return;

        let altitude = parseFloat(altInput.value) || 100;
        let gsd = parseFloat(gsdInput.value) || 2.5;

        // Validation Interceptor
        const validationWarningEl = document.getElementById('drone-mission-validation-warning');
        const validationWarningText = document.getElementById('mission-validation-text');
        const intervalWarningEl = document.getElementById('drone-interval-warning');
        
        const showValidationError = (msg) => {
            validationWarningText.innerText = msg;
            validationWarningEl.classList.remove('d-none');
            intervalWarningEl.classList.add('d-none');
            
            document.getElementById('res_gsd').innerText = 'N/A';
            document.getElementById('res_line_spacing').innerText = 'N/A';
            document.getElementById('res_photo_spacing').innerText = 'N/A';
            document.getElementById('res_photo_interval').innerText = 'N/A';
            
            this.currentSettings = { isValid: false };
        };
        
        validationWarningEl.classList.add('d-none');

        if (altitude <= 0 || !Number.isFinite(altitude)) return showValidationError('Altitude must be greater than 0.');
        if (speed <= 0 || !Number.isFinite(speed)) return showValidationError('Speed must be greater than 0.');
        if (frontOverlap < 0 || frontOverlap >= 100 || !Number.isFinite(frontOverlap)) return showValidationError('Front Overlap must be between 0% and 99%.');
        if (sideOverlap < 0 || sideOverlap >= 100 || !Number.isFinite(sideOverlap)) return showValidationError('Side Overlap must be between 0% and 99%.');

        // Resolve Truth based on Mode
        if (mode === 'altitude') {
            gsd = window.PhotogrammetryMath.calculateGSD(altitude, cameraSpec);
            gsdInput.value = gsd.toFixed(2);
        } else {
            altitude = window.PhotogrammetryMath.calculateAltitudeFromGSD(gsd, cameraSpec);
            altInput.value = altitude.toFixed(1);
        }

        // Calculate spacings based on optics
        const footprints = window.PhotogrammetryMath.calculateGroundFootprint(altitude, cameraSpec);
        const lineSpacing = window.PhotogrammetryMath.calculateLineSpacing(footprints.ground_width_m, sideOverlap);
        const shotSpacing = window.PhotogrammetryMath.calculateShotSpacing(footprints.ground_height_m, frontOverlap);
        
        if (lineSpacing <= 0 || !Number.isFinite(lineSpacing)) return showValidationError('Invalid flight line spacing generated.');
        if (shotSpacing <= 0 || !Number.isFinite(shotSpacing)) return showValidationError('Invalid photo spacing generated.');
        
        const photoInterval = speed > 0 ? (shotSpacing / speed) : 0;

        // Update basic UI stats
        document.getElementById('res_gsd').innerText = gsd.toFixed(2) + ' cm/pixel';
        document.getElementById('drone_gsd_val').value = gsd.toFixed(2);
        
        document.getElementById('res_line_spacing').innerText = lineSpacing.toFixed(1) + ' m';
        document.getElementById('drone_line_spacing_val').value = lineSpacing.toFixed(2);
        
        document.getElementById('res_photo_spacing').innerText = shotSpacing.toFixed(1) + ' m';
        document.getElementById('drone_photo_spacing_val').value = shotSpacing.toFixed(2);
        
        // Camera Capture Limit Validation
        const intervalWarningText = document.getElementById('interval-warning-text');
        
        if (captureMode === 'timed') {
            document.getElementById('res_photo_interval').innerText = photoInterval.toFixed(1) + ' s';
            document.getElementById('drone_photo_interval_val').value = photoInterval.toFixed(2);
            
            if (cameraSpec.min_photo_interval_sec) {
                if (photoInterval > 0 && photoInterval < cameraSpec.min_photo_interval_sec) {
                    intervalWarningText.innerText = `Required photo interval (${photoInterval.toFixed(1)}s) is below the selected camera's minimum capture interval (${cameraSpec.min_photo_interval_sec}s).`;
                    intervalWarningEl.classList.remove('d-none');
                } else {
                    intervalWarningEl.classList.add('d-none');
                }
            } else {
                intervalWarningText.innerText = "Minimum capture interval constraint is unavailable for this camera profile.";
                intervalWarningEl.classList.remove('d-none');
            }
        } else {
            document.getElementById('res_photo_interval').innerText = 'N/A (Distance Triggered)';
            document.getElementById('drone_photo_interval_val').value = 0;
            intervalWarningEl.classList.add('d-none'); // Hide warning since time doesn't matter
        }

        const gWidthEl = document.getElementById('drone_ground_width_val');
        if(gWidthEl) gWidthEl.value = footprints.ground_width_m.toFixed(2);
        
        const gHeightEl = document.getElementById('drone_ground_height_val');
        if(gHeightEl) gHeightEl.value = footprints.ground_height_m.toFixed(2);

        this.currentSettings = {
            isValid: true,
            droneSpec,
            cameraSpec,
            altitude,
            speed,
            frontOverlap,
            sideOverlap,
            margin,
            captureMode,
            gsd,
            lineSpacing,
            shotSpacing,
            photoInterval,
            ground_width_m: footprints.ground_width_m,
            ground_height_m: footprints.ground_height_m
        };
        
        // Re-calculate final metrics if lines already exist
        if (window.surveyCalcVars && window.surveyCalcVars.mainLineLengthsArray) {
            // CRITICAL FIX: Pass totalLength (Main + Turns) to accurately calculate flight duration!
            this.updateFinalMetrics(window.surveyCalcVars.totalLength, window.surveyCalcVars.mainLineLengthsArray);
        }
    },

    updateFinalMetrics: function(totalDistanceMeters, lineLengthsArray) {
        if (!this.currentSettings || !window.PhotogrammetryMath) return;
        
        const { droneSpec, speed, shotSpacing } = this.currentSettings;

        // 1. Total Images = sum of images per actual flight line segment
        let totalImages = 0;
        let validLinesCount = 0;
        
        if (Array.isArray(lineLengthsArray)) {
            lineLengthsArray.forEach(lineLength => {
                if (lineLength > 0) {
                    let imagesPerLine = Math.ceil(lineLength / shotSpacing) + 1;
                    totalImages += imagesPerLine;
                    validLinesCount++;
                }
            });
        }

        // 2. Pure Flight Duration (No buffers)
        const durationSeconds = speed > 0 ? (totalDistanceMeters / speed) : 0;
        const hours = Math.floor(durationSeconds / 3600);
        const minutes = Math.floor((durationSeconds % 3600) / 60);
        const seconds = Math.floor(durationSeconds % 60);

        // 3. Sorties (Based on drone's configured usable flight time)
        const durationMinutes = durationSeconds / 60;
        let sortieCount = 0;
        const sortieEl = document.getElementById('res_sorties');
        const usableTimeEl = document.getElementById('res_usable_time');
        
        if (droneSpec && droneSpec.usable_flight_time_min > 0) {
            sortieCount = Math.ceil(durationMinutes / droneSpec.usable_flight_time_min);
            if (sortieEl) sortieEl.innerText = Number.isFinite(sortieCount) && sortieCount >= 0 ? sortieCount.toLocaleString() : 'N/A';
            if (usableTimeEl) usableTimeEl.innerText = `Based on ${droneSpec.usable_flight_time_min} min usable flight time`;
        } else {
            if (sortieEl) {
                sortieEl.innerHTML = '<span class="text-danger" style="font-size:0.8rem;">Operational flight time not configured.</span>';
            }
            if (usableTimeEl) usableTimeEl.innerText = '';
        }

        // Validate final metrics
        let isDistanceValid = Number.isFinite(totalDistanceMeters) && totalDistanceMeters >= 0;
        let isImagesValid = Number.isFinite(totalImages) && totalImages >= 0;
        let isDurationValid = Number.isFinite(durationSeconds) && durationSeconds >= 0;

        // Update UI
        document.getElementById('res_flight_lines').innerText = validLinesCount;
        if(document.getElementById('float-lines')) document.getElementById('float-lines').innerText = validLinesCount;
        
        const distStr = isDistanceValid ? Math.round(totalDistanceMeters).toLocaleString() + ' m' : 'N/A';
        document.getElementById('res_distance').innerText = distStr;
        if(document.getElementById('float-distance')) document.getElementById('float-distance').innerText = distStr;
        document.getElementById('drone_total_distance').value = isDistanceValid ? totalDistanceMeters : '';
        
        const durStr = isDurationValid ? `${hours}h ${minutes}m ${seconds}s` : 'N/A';
        document.getElementById('res_duration').innerText = durStr;
        if(document.getElementById('float-duration')) document.getElementById('float-duration').innerText = durStr;
        document.getElementById('drone_duration_hours').value = isDurationValid ? (durationSeconds / 3600) : '';
        
        const imgStr = isImagesValid ? totalImages.toLocaleString() + ' images' : 'N/A';
        document.getElementById('res_images').innerText = imgStr;
        if(document.getElementById('float-images')) document.getElementById('float-images').innerText = isImagesValid ? totalImages.toLocaleString() : 'N/A';
        document.getElementById('drone_total_images').value = isImagesValid ? totalImages : '';

        document.getElementById('drone_sortie_count').value = sortieCount;
        if(document.getElementById('float-sorties')) document.getElementById('float-sorties').innerText = Number.isFinite(sortieCount) && sortieCount >= 0 ? sortieCount.toLocaleString() : 'N/A';
        document.getElementById('drone_usable_time_val').value = (droneSpec && droneSpec.usable_flight_time_min > 0) ? droneSpec.usable_flight_time_min : '';
    }
};

if (typeof window !== 'undefined') {
    window.DroneUI = DroneUI;
}


// ============================================================
// DATA STORES
// ============================================================
let boundaryFeatures = [];
let boundaryLayer = null;
let lineFeatures = [];

let map;
let drawnItems;
let drawControl;
let isGeometryStale = false;

// Layer groups for toggling
let boundaryLayerGroup = (typeof L !== 'undefined') ? L.layerGroup() : null;
let mainLineLayerGroup = (typeof L !== 'undefined') ? L.layerGroup() : null;
let crossLineLayerGroup = (typeof L !== 'undefined') ? L.layerGroup() : null;
let labelLayerGroup = (typeof L !== 'undefined') ? L.layerGroup() : null;

// Base layers
let baseLayers = {};
let activeBaseLayer = null;

function markStale() {
    let hasGeneratedLines = false;
    mainLineLayerGroup.eachLayer(l => { hasGeneratedLines = true; });
    crossLineLayerGroup.eachLayer(l => { hasGeneratedLines = true; });
    if (hasGeneratedLines) {
        isGeometryStale = true;
        let warningEl = document.getElementById('stale-warning');
        if (warningEl) warningEl.classList.remove('d-none');
    }
}

// ============================================================
// INITIALIZATION
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    const panelToggle = document.getElementById('btn-toggle-planning-panel');
    const workspace = document.querySelector('.workspace-container');
    if (panelToggle && workspace) {
        panelToggle.addEventListener('click', function () {
            const collapsed = workspace.classList.toggle('is-panel-collapsed');
            panelToggle.setAttribute('aria-expanded', String(!collapsed));
            panelToggle.setAttribute('aria-label', collapsed ? 'Show planning controls' : 'Hide planning controls');
            panelToggle.innerHTML = collapsed
                ? '<i class="fa-solid fa-sliders" aria-hidden="true"></i><span>Show controls</span>'
                : '<i class="fa-solid fa-sliders" aria-hidden="true"></i><span>Hide controls</span>';
            window.setTimeout(function () { if (map) map.invalidateSize({ pan: false }); }, 220);
        });
    }

    // 1. Initialize Map
    if (!boundaryLayerGroup && typeof L !== 'undefined') boundaryLayerGroup = L.layerGroup();
    if (!mainLineLayerGroup && typeof L !== 'undefined') mainLineLayerGroup = L.layerGroup();
    if (!crossLineLayerGroup && typeof L !== 'undefined') crossLineLayerGroup = L.layerGroup();
    if (!labelLayerGroup && typeof L !== 'undefined') labelLayerGroup = L.layerGroup();

    map = L.map('map', { zoomControl: true }).setView([4.2105, 101.9758], 6);
    window.map = map;
    window.isesMap = map;

    window.addEventListener('resize', function () {
        if (map) map.invalidateSize({ pan: false });
    });

    // 2. Base Layers
    baseLayers.osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19, attribution: '&copy; OpenStreetMap'
    });
    baseLayers.satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19, attribution: '&copy; Esri'
    });
    baseLayers.ocean = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Ocean/World_Ocean_Base/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 13, attribution: '&copy; Esri Ocean'
    });
    baseLayers.dark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19, attribution: '&copy; CartoDB'
    });

    activeBaseLayer = baseLayers.osm;
    activeBaseLayer.addTo(map);

    // Basemap bridge: React owns selection state; Leaflet owns tile-layer lifecycle.
    document.addEventListener('ises:basemap-change', function (event) {
        const key = event.detail && event.detail.basemap;
        if (!key || !baseLayers[key] || key === activeBaseLayer) return;
        if (activeBaseLayer && map.hasLayer(activeBaseLayer)) map.removeLayer(activeBaseLayer);
        activeBaseLayer = baseLayers[key];
        activeBaseLayer.addTo(map);
    });

    // 3. Scale bar
    L.control.scale({ position: 'bottomright', imperial: false }).addTo(map);

    // 4. Coordinate display
    map.on('mousemove', function (e) {
        let coordEl = document.getElementById('coord-display');
        coordEl.innerHTML = 'Lat: <span>' + e.latlng.lat.toFixed(6) + '</span> &nbsp; Lng: <span>' + e.latlng.lng.toFixed(6) + '</span>';
    });

    // 5. Layer groups
    boundaryLayerGroup.addTo(map);
    mainLineLayerGroup.addTo(map);
    crossLineLayerGroup.addTo(map);
    labelLayerGroup.addTo(map);

    // 6. Leaflet Draw
    drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    drawControl = new L.Control.Draw({
        draw: {
            polygon: {
                shapeOptions: { color: '#3b82f6', weight: 3, fillOpacity: 0.15 }
            },
            polyline: {
                shapeOptions: { color: '#f59e0b', weight: 2, dashArray: '6, 8' }
            },
            marker: false, circle: false, circlemarker: false, rectangle: false
        },
        edit: { featureGroup: drawnItems }
    });
    map.addControl(drawControl);

    window.drawnItems = drawnItems;
    window.drawControl = drawControl;
    window.boundaryLayerGroup = boundaryLayerGroup;
    window.mainLineLayerGroup = mainLineLayerGroup;
    window.crossLineLayerGroup = crossLineLayerGroup;
    window.labelLayerGroup = labelLayerGroup;

    // 7. Load Existing Data
    loadExistingData();

    // Recalculate Leaflet's viewport after the shared shell and workspace grid settle.
    const refreshMapSize = function () {
        if (map && map.getContainer() && map.getContainer().offsetWidth > 0 && map.getContainer().offsetHeight > 0) {
            map.invalidateSize({ pan: false, debounceMoveend: true });
        }
    };
    window.requestAnimationFrame(refreshMapSize);
    window.setTimeout(refreshMapSize, 250);
    window.setTimeout(refreshMapSize, 800);

    // 8. Draw Events
    map.on(L.Draw.Event.CREATED, function (e) {
        var type = e.layerType, layer = e.layer;
        if (!map.hasLayer(drawnItems)) {
            drawnItems.addTo(map);
            var bCheck = document.getElementById('layer-boundaries');
            if (bCheck) bCheck.checked = true;
        }
        drawnItems.addLayer(layer);
        recalculateAllStats();
    });

    map.on(L.Draw.Event.EDITED, function (e) {
        markStale();
        rebuildLayerGroups();
        recalculateAllStats();
    });

    map.on(L.Draw.Event.DELETED, function (e) {
        markStale();
        rebuildLayerGroups();
        recalculateAllStats();
    });

    // 9. Generator buttons
    let btnGenMain = document.getElementById('btn-generate-main');
    if (btnGenMain) {
        btnGenMain.addEventListener('click', () => generateLines('main'));
        document.getElementById('btn-generate-cross').addEventListener('click', () => generateLines('cross'));

        // 10. Clear lines
        document.getElementById('btn-clear-lines').addEventListener('click', function () {
            let toRemove = [];
            drawnItems.eachLayer(function (layer) {
                if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
                    toRemove.push(layer);
                }
            });
            toRemove.forEach(l => drawnItems.removeLayer(l));
            mainLineLayerGroup.clearLayers();
            crossLineLayerGroup.clearLayers();
            labelLayerGroup.clearLayers();
            isGeometryStale = false;
            let warningEl = document.getElementById('stale-warning');
            if (warningEl) warningEl.classList.add('d-none');
            recalculateAllStats();
        });

        // 11. Parameter change listeners
        document.getElementById('gen-mode').addEventListener('change', function () {
            if (this.value === 'centerline') {
                document.getElementById('gen-mode-polygon').classList.add('d-none');
                document.getElementById('gen-mode-centerline').classList.remove('d-none');
            } else {
                document.getElementById('gen-mode-polygon').classList.remove('d-none');
                document.getElementById('gen-mode-centerline').classList.add('d-none');
            }
        });
        document.getElementById('gen-spacing').addEventListener('input', markStale);
        document.getElementById('gen-angle').addEventListener('input', markStale);
        document.getElementById('gen-cross-spacing').addEventListener('input', markStale);

        document.getElementById('gen-cl-spacing').addEventListener('input', markStale);
        document.getElementById('gen-cl-left').addEventListener('input', markStale);
        document.getElementById('gen-cl-right').addEventListener('input', markStale);
    }

    // 11b. Image Overlay Logic
    let currentImageOverlay = null;
    let overlayMarkers = [];

    let btnAddOverlay = document.getElementById('btn-add-overlay');
    if (btnAddOverlay) {
        btnAddOverlay.addEventListener('click', function () {
            let fileInput = document.getElementById('file-overlay-image');
            if (fileInput.files.length === 0) {
                Swal.fire('No Image', 'Please select an image file first.', 'warning');
                return;
            }

            let file = fileInput.files[0];
            let reader = new FileReader();
            reader.onload = function (e) {
                let imgUrl = e.target.result;
                let bounds = map.getBounds();

                // Create an overlay slightly smaller than current view
                let sw = bounds.getSouthWest();
                let ne = bounds.getNorthEast();
                let latMargin = (ne.lat - sw.lat) * 0.2;
                let lngMargin = (ne.lng - sw.lng) * 0.2;

                let imageBounds = [[sw.lat + latMargin, sw.lng + lngMargin], [ne.lat - latMargin, ne.lng - lngMargin]];

                if (currentImageOverlay) {
                    map.removeLayer(currentImageOverlay);
                    overlayMarkers.forEach(m => map.removeLayer(m));
                    overlayMarkers = [];
                }

                currentImageOverlay = L.imageOverlay(imgUrl, imageBounds, { opacity: 0.7, zIndex: 1 }).addTo(map);

                // Add draggable corner markers for basic georeferencing
                let tlMarker = L.marker([imageBounds[1][0], imageBounds[0][1]], { draggable: true }).addTo(map);
                let brMarker = L.marker([imageBounds[0][0], imageBounds[1][1]], { draggable: true }).addTo(map);

                overlayMarkers.push(tlMarker, brMarker);

                function updateImageBounds() {
                    let tl = tlMarker.getLatLng();
                    let br = brMarker.getLatLng();
                    currentImageOverlay.setBounds([[br.lat, tl.lng], [tl.lat, br.lng]]);
                }

                tlMarker.on('drag', updateImageBounds);
                brMarker.on('drag', updateImageBounds);

                Swal.fire('Image Placed', 'Drag the two markers to scale and position the image.', 'success');
            };
            reader.readAsDataURL(file);
        });
    }

    // 12. Time estimation reactivity (handled by inline onchange in HTML)

    // Drone Mapping UI Initialization
    window.isDroneMode = window.MAP_WORKSPACE_CONFIG.surveyType === 'drone';
    window.EQUIPMENT_DB_URL = window.MAP_WORKSPACE_CONFIG.equipmentUrl;
    if (window.isDroneMode && typeof DroneUI !== 'undefined') {
        const savedParams = window.MAP_WORKSPACE_CONFIG.droneParameters;
        DroneUI.init('drone-ui-container', savedParams);
        const droneAcc = document.getElementById('droneMappingAccordionItem');
        if (droneAcc) droneAcc.style.display = 'block';
    }

    // Define the global callback for Drone Mapping Line Generation
    window.requestDroneLinesGeneration = function () {
        if (!DroneUI.currentSettings) return;

        if (DroneUI.currentSettings.isValid === false) {
            Swal.fire('Validation Error', 'Please fix the invalid mission parameters before generating the flight path.', 'error');
            return;
        }

        // 1. Calculate side lap spacing in meters
        const footprints = PhotogrammetryMath.calculateGroundFootprint(
            DroneUI.currentSettings.altitude,
            DroneUI.currentSettings.cameraSpec
        );
        const lineSpacingMeters = PhotogrammetryMath.calculateLineSpacing(
            footprints.ground_width_m,
            DroneUI.currentSettings.sideOverlap
        );

        let isAutoAngle = document.getElementById('angle_mode_auto') && document.getElementById('angle_mode_auto').checked;
        let angle = parseFloat(document.getElementById('drone_course_angle').value) || 0;
        let margin = DroneUI.currentSettings.margin || 0;

        // 2. Clear old lines
        mainLineLayerGroup.clearLayers();
        crossLineLayerGroup.clearLayers();
        labelLayerGroup.clearLayers();

        // Clear any Start/End markers specifically if they exist outside labelLayerGroup
        if (window.routeMarkers) {
            window.routeMarkers.forEach(m => map.removeLayer(m));
        }
        window.routeMarkers = [];

        let toRemove = [];
        drawnItems.eachLayer(function (layer) {
            if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
                toRemove.push(layer);
            }
        });
        toRemove.forEach(l => drawnItems.removeLayer(l));

        if (boundaryFeatures.length === 0) {
            Swal.fire('No Boundary', 'Please draw a survey boundary polygon first.', 'warning');
            return;
        }

        let originalBoundary = boundaryFeatures[0];
        let effectiveBoundary = originalBoundary;

        // Apply inward margin
        if (margin > 0) {
            try {
                let buffered = turf.buffer(originalBoundary, -margin, { units: 'meters' });
                if (!buffered || !buffered.geometry || buffered.geometry.type !== 'Polygon' && buffered.geometry.type !== 'MultiPolygon') {
                    Swal.fire('Margin Too Large', 'The mapping margin has collapsed the survey area entirely. Reverting to original area.', 'warning');
                } else {
                    effectiveBoundary = buffered;
                }
            } catch (e) {
                console.error("Buffer error:", e);
                Swal.fire('Margin Error', 'Could not apply margin. Reverting to original area.', 'warning');
            }
        }

        // Expose effective boundary for generation
        window.droneEffectiveBoundary = effectiveBoundary;

        // Draw Effective Boundary as a dashed orange line
        if (effectiveBoundary !== originalBoundary) {
            let effLayer = L.geoJSON(effectiveBoundary, {
                style: { color: '#f97316', weight: 2, dashArray: '5, 5', fillOpacity: 0.1 },
                interactive: false
            });
            // We don't want to add it to drawnItems because it's derived. We'll add it to boundaryLayerGroup.
            boundaryLayerGroup.addLayer(effLayer);
        }

        // Auto Angle Calculation
        if (isAutoAngle) {
            try {
                let hull = turf.convex(effectiveBoundary);
                if (hull && hull.geometry && hull.geometry.coordinates && hull.geometry.coordinates[0]) {
                    let coords = hull.geometry.coordinates[0];
                    let longestDist = -1;
                    let bestAngle = 0;

                    for (let i = 0; i < coords.length - 1; i++) {
                        let pt1 = turf.point(coords[i]);
                        let pt2 = turf.point(coords[i + 1]);
                        let dist = turf.distance(pt1, pt2, { units: 'meters' });
                        if (dist > longestDist) {
                            longestDist = dist;
                            bestAngle = turf.bearing(pt1, pt2);
                        }
                    }

                    // Normalize angle to 0-359
                    if (bestAngle < 0) bestAngle += 360;
                    angle = bestAngle;
                    document.getElementById('drone_course_angle').value = Math.round(angle);
                }
            } catch (e) {
                console.error("Auto angle error:", e);
            }
        }

        // 3. Inject settings into global window variables instead of missing DOM elements
        window.droneTempSpacing = lineSpacingMeters;
        window.droneTempAngle = angle;

        // 4. Trigger standard line generation
        generateLines('main', true, function (totalDistanceMeters, lineLengthsArray) {
            // This callback runs after lines are generated and distance is known
            DroneUI.updateFinalMetrics(totalDistanceMeters, lineLengthsArray);

            // Add Start and End markers
            let layers = mainLineLayerGroup.getLayers();
            if (layers.length > 0) {
                let firstLineCoords = null;
                let lastLineCoords = null;

                // Because the worker might return multiple features, we need the first and last point
                // But turf might return one big MultiLineString or multiple LineStrings.
                // mainLineLayerGroup will have L.geoJSON wrappers.
                layers.forEach(layer => {
                    if (layer.getLayers) {
                        layer.getLayers().forEach(sublayer => {
                            if (sublayer.feature && sublayer.feature.geometry.type === 'LineString') {
                                let coords = sublayer.feature.geometry.coordinates;
                                if (coords.length > 0) {
                                    if (!firstLineCoords) firstLineCoords = coords[0];
                                    lastLineCoords = coords[coords.length - 1];
                                }
                            }
                        });
                    }
                });

                if (firstLineCoords && lastLineCoords) {
                    let startMarker = L.circleMarker([firstLineCoords[1], firstLineCoords[0]], {
                        radius: 6, fillColor: '#22c55e', color: '#fff', weight: 2, fillOpacity: 1
                    }).bindTooltip('Start');
                    let endMarker = L.circleMarker([lastLineCoords[1], lastLineCoords[0]], {
                        radius: 6, fillColor: '#ef4444', color: '#fff', weight: 2, fillOpacity: 1
                    }).bindTooltip('End');

                    startMarker.addTo(map);
                    endMarker.addTo(map);
                    window.routeMarkers.push(startMarker, endMarker);
                }
            }
        });
    };


    // 13. Save Planning
    document.querySelectorAll('.btn-save-planning-trigger').forEach(btn => {
        btn.addEventListener('click', saveSurveyPlanning);
    });

    // 14. Layer toggle listeners
    function toggleFeatureLayer(id, isChecked) {
        if (id === 'layer-boundaries') {
            if (drawnItems) {
                isChecked ? drawnItems.addTo(map) : map.removeLayer(drawnItems);
            }
            if (boundaryLayerGroup) {
                isChecked ? boundaryLayerGroup.addTo(map) : map.removeLayer(boundaryLayerGroup);
            }
        } else if (id === 'layer-main-lines' && mainLineLayerGroup) {
            isChecked ? mainLineLayerGroup.addTo(map) : map.removeLayer(mainLineLayerGroup);
        } else if (id === 'layer-cross-lines' && crossLineLayerGroup) {
            isChecked ? crossLineLayerGroup.addTo(map) : map.removeLayer(crossLineLayerGroup);
        } else if (id === 'layer-labels' && labelLayerGroup) {
            isChecked ? labelLayerGroup.addTo(map) : map.removeLayer(labelLayerGroup);
        }
    }

    ['layer-boundaries', 'layer-main-lines', 'layer-cross-lines', 'layer-labels'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', function () {
                toggleFeatureLayer(id, this.checked);
            });
        }
    });

    document.addEventListener('change', function (e) {
        if (e.target && e.target.id && e.target.id.startsWith('layer-')) {
            toggleFeatureLayer(e.target.id, e.target.checked);
        }
    });
});

// ============================================================
// LOAD EXISTING DATA
// ============================================================
function loadExistingData() {
    var boundaries = window.MAP_WORKSPACE_CONFIG.boundaries || [];
    boundaries.forEach(function (geoJsonFeature) {
        var layer = L.geoJSON(geoJsonFeature, { style: { color: '#3b82f6', weight: 3, fillOpacity: 0.15 } });
        layer.eachLayer(function (l) {
            drawnItems.addLayer(l);
        });
    });

    let ts = new Date().getTime();
    console.log("DEBUG: Fetching lines on page load! URL=", window.MAP_WORKSPACE_CONFIG.linesUrl + "?t=" + ts);
    fetch(window.MAP_WORKSPACE_CONFIG.linesUrl + "?t=" + ts)
        .then(response => {
            console.log("DEBUG: Fetch lines response received", response);
            return response.json();
        })
        .then(existingLines => {
            console.log("DEBUG: Fetch lines JSON parsed. Length=", existingLines?.features?.length || 0, existingLines);
            if (existingLines && existingLines.features && existingLines.features.length > 0) {
                try {
                    existingLines.features.forEach(function (geoJsonFeature) {
                        var lineType = (geoJsonFeature.properties && geoJsonFeature.properties.line_type) || 'main';
                        var isMain = lineType === 'main';

                        var color = isMain ? '#f43f5e' : '#f59e0b';
                        var lineLayer = L.geoJSON(geoJsonFeature, {
                            style: { color: color, weight: isMain ? 1.5 : 1, opacity: 0.8, dashArray: isMain ? '' : '6, 8' }
                        });

                        lineLayer.eachLayer(function (l) {
                            l.isGenerated = true;
                            l.lineType = lineType;
                            if (isMain) {
                                mainLineLayerGroup.addLayer(l);
                            } else {
                                crossLineLayerGroup.addLayer(l);
                            }
                        });
                    });
                } catch (err) {
                    console.error("Error drawing existing lines:", err);
                    Swal.fire('Drawing Error', 'An error occurred while drawing existing lines: ' + err.message, 'error');
                }
            }

            recalculateAllStats();

            // Synchronize Drone UI with re-calculated metrics to replace stale saved DB values
            if (window.isDroneMode && typeof DroneUI !== 'undefined' && window.surveyCalcVars && window.surveyCalcVars.mainLineLengthsArray) {
                DroneUI.updateFinalMetrics(window.surveyCalcVars.totalLength, window.surveyCalcVars.mainLineLengthsArray);
            }

            if (drawnItems.getLayers().length > 0) {
                map.fitBounds(drawnItems.getBounds());
            } else if (mainLineLayerGroup.getLayers().length > 0) {
                map.fitBounds(mainLineLayerGroup.getBounds());
            } else if (window.MAP_WORKSPACE_CONFIG.projectLocation) {
                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(window.MAP_WORKSPACE_CONFIG.projectLocation))
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.length > 0) {
                            let b = data[0].boundingbox;
                            map.fitBounds([[b[0], b[2]], [b[1], b[3]]]);
                        }
                    });
            }
        })
        .catch(error => {
            console.error('Error loading map lines:', error);
            Swal.fire('Fetch Error', 'Failed to load lines from the server. Check console for details. Error: ' + error.message, 'error');
        });
}

// ============================================================
// REBUILD LAYER GROUPS (after edit/delete)
// ============================================================
function rebuildLayerGroups() {
    boundaryLayerGroup.clearLayers();
    // In drone mode, re-draw effective boundary if margin is applied and different
    if (window.droneEffectiveBoundary && window.droneEffectiveBoundary !== window.droneOriginalBoundary) {
        let effLayer = L.geoJSON(window.droneEffectiveBoundary, {
            style: { color: '#f97316', weight: 2, dashArray: '5, 5', fillOpacity: 0.1 },
            interactive: false
        });
        boundaryLayerGroup.addLayer(effLayer);
    }
}

// ============================================================
// RECALCULATE ALL STATISTICS
// RECALCULATE ALL STATISTICS
// ============================================================
function recalculateAllStats() {
    console.log("DEBUG: --- recalculateAllStats() START ---");
    console.log("DEBUG: drawnItems count before calculation:", drawnItems.getLayers().length);
    try {
        let mainCount = 0, crossCount = 0;
        let mainLength = 0, crossLength = 0;
        let boundaryArea = 0, boundaryPerimeter = 0, verticesCount = 0;
        let mainLineLengths = []; // Added for Drone Mapping actual per-line image calculation
        boundaryFeatures = [];

        let genModeEl = document.getElementById('gen-mode');
        let mode = genModeEl ? genModeEl.value : (window.isDroneMode ? 'polygon' : 'polygon');
        let foundCenterline = false;

        drawnItems.eachLayer(function (layer) {
            if (layer instanceof L.Polygon) {
                var geojson = layer.toGeoJSON();
                boundaryFeatures.push(geojson);

                let pArea = turf.area(geojson);
                let polyLine = turf.polygonToLine(geojson);
                let pPerim = turf.length(polyLine, { units: 'meters' });

                boundaryArea += pArea;
                boundaryPerimeter += pPerim;

                // Map Tooltip: Show KM stats on hover
                let areaKm2 = (pArea / 1000000).toLocaleString(undefined, { maximumFractionDigits: 2 });
                let perimKm = (pPerim / 1000).toLocaleString(undefined, { maximumFractionDigits: 2 });
                layer.bindTooltip(`Area: ${areaKm2} km²<br>Perimeter: ${perimKm} km`, {
                    permanent: false,
                    direction: 'center',
                    className: 'bg-dark text-white border-0 shadow-sm'
                });

                if (geojson.geometry && geojson.geometry.coordinates && geojson.geometry.coordinates[0]) {
                    let coords = geojson.geometry.coordinates[0];
                    verticesCount += (coords.length > 0 ? coords.length - 1 : 0);
                }
            }
        });

        function processPolylineLayer(layer) {
            if (!(layer instanceof L.Polyline) || (layer instanceof L.Polygon)) return;
            var geojson = layer.toGeoJSON();
            let len = 0;
            let coords = (geojson.geometry && geojson.geometry.coordinates) ? geojson.geometry.coordinates : [];
            if (!coords || coords.length === 0) return;
            let isCross = false;
            let isMain = false;

            if (layer.isGenerated) {
                isCross = layer.lineType === 'cross';
                isMain = !isCross;
            } else {
                // For manually drawn lines:
                if (mode === 'centerline' && !foundCenterline) {
                    // The first manually drawn line in centerline mode is the reference line, do not count it!
                    foundCenterline = true;
                    return;
                } else {
                    // Any other manually drawn line is treated as a custom Tie/Cross line by the surveyor
                    isCross = true;
                    layer.lineType = 'cross'; // Ensure it's marked so we can style/clear it if needed
                }
            }

            if (window.isDroneMode && layer.isGenerated && isMain) {
                let currentLineLength = 0;
                let baseBearing = null;

                for (let i = 0; i < coords.length - 1; i++) {
                    let p1 = turf.point(coords[i]);
                    let p2 = turf.point(coords[i + 1]);
                    let segLen = map.distance(
                        L.latLng(coords[i][1], coords[i][0]),
                        L.latLng(coords[i + 1][1], coords[i + 1][0])
                    );
                    len += segLen;

                    if (segLen < 0.1) continue;

                    let rawBearing = turf.bearing(p1, p2);
                    let bearing = (rawBearing + 360) % 180;
                    if (baseBearing === null) baseBearing = bearing;

                    let diff = Math.abs(bearing - baseBearing);
                    let isParallel = diff < 10 || diff > 170;

                    if (isParallel) {
                        currentLineLength += segLen;
                    } else {
                        if (currentLineLength > 0) {
                            mainLineLengths.push(currentLineLength);
                            currentLineLength = 0;
                        }
                    }
                }
                if (currentLineLength > 0) {
                    mainLineLengths.push(currentLineLength);
                }

                mainLength += len;
                mainCount += mainLineLengths.length;
            } else {
                for (let i = 0; i < coords.length - 1; i++) {
                    len += map.distance(
                        L.latLng(coords[i][1], coords[i][0]),
                        L.latLng(coords[i + 1][1], coords[i + 1][0])
                    );
                }

                if (isCross) {
                    crossLength += len;
                    crossCount++;
                } else if (isMain) {
                    mainLength += len;
                    mainCount++;
                    mainLineLengths.push(len); // Store true length for each segment
                }
            }

            layer.feature = layer.feature || { type: "Feature", properties: {} };
            layer.feature.properties.length = len;
            layer.feature.properties.line_type = isCross ? 'cross' : (isMain ? 'main' : 'reference');
        }

        // Process any manual polylines drawn in drawnItems
        drawnItems.eachLayer(function (layer) {
            if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
                processPolylineLayer(layer);
            }
        });
        // Process main and cross survey lines
        mainLineLayerGroup.eachLayer(processPolylineLayer);
        crossLineLayerGroup.eachLayer(processPolylineLayer);

        let coverageWidth = 0; // perpendicular extent
        let lineLength = 0;    // length of each individual survey line (shortest side)

        if (boundaryFeatures.length > 0) {
            let genAngleEl = document.getElementById('gen-angle');
            let angle = genAngleEl ? (parseFloat(genAngleEl.value) || 0) : 0;
            let fc = turf.featureCollection(boundaryFeatures);
            let center = turf.center(fc);

            let bounds = window.calculateTrueMetricBounds(fc, angle, turf, map);

            let widthX = bounds.widthX;
            let heightY = bounds.heightY;

            // INOS Required Logic: Survey Width (line length) MUST be the shortest side
            lineLength = Math.min(widthX, heightY);
            coverageWidth = Math.max(widthX, heightY);

            if (document.getElementById('stat-survey-width')) {
                document.getElementById('stat-survey-width').innerText = (lineLength / 1000).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' km';
            }
        } else {
            if (document.getElementById('stat-survey-width')) {
                document.getElementById('stat-survey-width').innerText = '0.00 km';
            }
        }

        // FORMULA: Main Distance = Number of Main Lines × Survey Width (lineLength)
        // CRITICAL FIX: For Drone Mapping, use the exact sum of true GIS segment lengths (mainLength), 
        // NOT the bounding box approximation.
        let mainLengthMeters = window.isDroneMode ? mainLength : (mainCount * lineLength);

        // Crossline Distance = actual real-world length calculated (crossLength)
        let totalLength = mainLengthMeters + crossLength;

        // Expose globally for saveSurveyPlanning to use
        window.currentTotalLengthMeters = totalLength;

        // Convert distances to NM
        let mainNM = (mainLengthMeters / 1000) / 1.852;
        let crossNM = (crossLength / 1000) / 1.852;
        let totalNM = mainNM + crossNM;



        // Expose individual calculation variables globally for the UI
        window.surveyCalcVars = {
            mainCount: mainCount,
            crossCount: crossCount,
            lineLength: lineLength,
            mainLengthMeters: mainLengthMeters,
            crossLength: crossLength,
            totalLength: totalLength,
            mainNM: mainNM,
            crossNM: crossNM,
            totalNM: totalNM,
            mainLineLengthsArray: mainLineLengths // Export array of exact segment lengths
        };

        // Update UI elements if they exist
        function updateIfExist(id, val) {
            let el = document.getElementById(id);
            if (el) el.innerText = val;
        }

        // Boundary
        updateIfExist('stat-boundary-area', boundaryArea.toLocaleString(undefined, { maximumFractionDigits: 2 }) + ' m²');
        updateIfExist('stat-eng-boundary-area', boundaryArea.toLocaleString(undefined, { maximumFractionDigits: 2 }) + ' m²');
        updateIfExist('res_area', boundaryArea.toLocaleString(undefined, { maximumFractionDigits: 2 }) + ' m²');

        updateIfExist('stat-boundary-perimeter', (boundaryPerimeter / 1000).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' km');
        updateIfExist('stat-eng-boundary-perimeter', (boundaryPerimeter / 1000).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' km');
        updateIfExist('res_perimeter', (boundaryPerimeter / 1000).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' km');

        updateIfExist('stat-survey-length', (coverageWidth / 1000).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' km');

        // Main Lines
        updateIfExist('stat-main-count', mainCount);
        updateIfExist('stat-main-dist-km', (mainLengthMeters / 1000).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' km');
        updateIfExist('stat-main-dist-nm', mainNM.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' NM');

        // Cross Lines
        updateIfExist('stat-cross-count', crossCount);
        updateIfExist('stat-cross-dist-km', (crossLength / 1000).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' km');
        updateIfExist('stat-cross-dist-nm', crossNM.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' NM');

        // Total Distance
        updateIfExist('stat-total-nm', totalNM.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' NM'); const mirrorMapMetric = (sourceId, targetId) => {
            const source = document.getElementById(sourceId);
            const target = document.getElementById(targetId);
            if (source && target) target.textContent = source.textContent || target.textContent;
        };
        mirrorMapMetric('stat-boundary-area', 'map-summary-stat-boundary-area');
        mirrorMapMetric('stat-main-count', 'map-summary-stat-main-count');
        mirrorMapMetric('stat-cross-count', 'map-summary-stat-cross-count');
        mirrorMapMetric('stat-total-nm', 'map-summary-stat-total-nm');

        // Update Metric Strip (always-visible top bar)
        updateIfExist('ms-total-nm', totalNM.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' NM');
        updateIfExist('ms-total-lines', (mainCount + crossCount));

        calculateTimeEstimation();
        console.log("DEBUG: --- recalculateAllStats() END SUCCESS ---");
    } catch (err) {
        console.error("DEBUG: !!! FATAL ERROR IN recalculateAllStats !!!");
        console.error(err);
        Swal.fire('Calculation Error', 'An error occurred while calculating statistics: ' + err.message, 'error');
    }
}

// ============================================================
// TIME ESTIMATION
// ============================================================
function calculateTimeEstimation() {
    let speedKnots = parseFloat(document.getElementById('sbes-speed')?.value) || 0;
    let workHrs = parseFloat(document.getElementById('sbes-workhrs')?.value) || 0;
    let weatherDays = parseFloat(document.getElementById('project-weather')?.value) || 0;
    let mobDays = parseFloat(document.getElementById('project-mob')?.value) || 0;
    let patchDays = parseFloat(document.getElementById('project-patch')?.value) || 0;

    let totalNm = window.surveyCalcVars ? window.surveyCalcVars.totalNM : 0;
    let workingDays = 0;
    let surveyHours = 0;

    if (speedKnots > 0 && totalNm > 0) {
        // FORMULA: Survey Time (hours) = Total Survey Distance (NM) / Survey Speed (knots)
        surveyHours = totalNm / speedKnots;

        let elHours = document.getElementById('calc-survey-hours');
        if (elHours) elHours.innerText = surveyHours.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' hours';

        let msHours = document.getElementById('ms-survey-hours');
        if (msHours) msHours.innerText = surveyHours.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        if (workHrs > 0) {
            // FORMULA: Survey Execution Days = Total Survey Time (hours) / Working Hours Per Day (Exact fractional days)
            workingDays = surveyHours / workHrs;
        }
    } else {
        let elHours = document.getElementById('calc-survey-hours');
        if (elHours) elHours.innerText = '0.00 hours';
        let msHours = document.getElementById('ms-survey-hours');
        if (msHours) msHours.innerText = '0.00';
    }

    // FORMULA: Estimated Project Duration = Survey Days + global allowances
    let totalDays = workingDays + weatherDays + mobDays + patchDays;

    let elWorkingDays = document.getElementById('calc-working-days');
    if (elWorkingDays) elWorkingDays.innerText = totalDays.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' days';

    let msDays = document.getElementById('ms-working-days');
    if (msDays) msDays.innerText = totalDays.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ============================================================
// LOADING OVERLAY
// ============================================================
function showMapLoading(text) {
    document.getElementById('map-loading-text').innerText = text;
    document.getElementById('map-loading-overlay').classList.remove('d-none');
    document.getElementById('map-loading-overlay').classList.add('d-flex');
}
function hideMapLoading() {
    document.getElementById('map-loading-overlay').classList.add('d-none');
    document.getElementById('map-loading-overlay').classList.remove('d-flex');
}

// ============================================================
// LINE GENERATION (Main or Cross)
// ============================================================
function generateLines(type, isDrone = false, callback = null) {
    let genModeEl = document.getElementById('gen-mode');
    let mode = genModeEl ? genModeEl.value : (isDrone ? 'polygon' : 'polygon');
    let centerlineFeature = null;

    if (mode === 'centerline' && type === 'main') {
        drawnItems.eachLayer(function (layer) {
            // Find a drawn polyline that is not a polygon and not generated
            if (layer instanceof L.Polyline && !(layer instanceof L.Polygon) && !layer.isGenerated) {
                centerlineFeature = layer.toGeoJSON();
            }
        });

        // If there's no boundary but we have a centerline, we can technically still generate, 
        // but for clipping we need a boundary. Let's warn if neither.
        if (boundaryFeatures.length === 0) {
            Swal.fire('No Boundary', 'Please draw a boundary polygon to clip the lines.', 'warning');
            return;
        }

        if (!centerlineFeature) {
            Swal.fire('No Centerline', 'Please draw a line on the map using the Polyline tool to act as the centerline.', 'warning');
            hideMapLoading();
            return;
        }
    } else {
        if (boundaryFeatures.length === 0) {
            Swal.fire('No Boundary', 'Please draw at least one survey boundary polygon first.', 'warning');
            return;
        }
    }

    let spacingMeters, angle;

    if (isDrone) {
        spacingMeters = window.droneTempSpacing || 10;
        angle = window.droneTempAngle || 0;
    } else if (type === 'cross') {
        let genCrossEl = document.getElementById('gen-cross-spacing');
        spacingMeters = genCrossEl ? parseFloat(genCrossEl.value) : 100;
        let genAngleEl = document.getElementById('gen-angle');
        let baseAngle = genAngleEl ? (parseFloat(genAngleEl.value) || 0) : 0;
        // Hardcode tie lines to strictly 90 degrees offset from main lines
        let crossAngle = 90;
        angle = baseAngle + crossAngle;

        if (!spacingMeters || spacingMeters <= 0) {
            Swal.fire('Missing Input', 'Please enter a Cross Line spacing value.', 'warning');
            return;
        }
    } else {
        let genSpacingEl = document.getElementById('gen-spacing');
        spacingMeters = genSpacingEl ? parseFloat(genSpacingEl.value) : 10;
        let genAngleEl = document.getElementById('gen-angle');
        angle = genAngleEl ? (parseFloat(genAngleEl.value) || 0) : 0;
    }

    let loadingText = type === 'cross' ? 'Generating Tie Lines...' : 'Generating Main Lines...';
    showMapLoading(loadingText);

    // Clear existing lines of this type
    let toRemove = [];
    drawnItems.eachLayer(function (layer) {
        if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
            if (type === 'main' && (layer.lineType === 'main' || !layer.lineType) && layer.isGenerated) {
                toRemove.push(layer);
            } else if (type === 'cross' && layer.lineType === 'cross' && layer.isGenerated) {
                toRemove.push(layer);
            }
        }
    });
    toRemove.forEach(l => drawnItems.removeLayer(l));

    if (type === 'main') {
        mainLineLayerGroup.clearLayers();
        labelLayerGroup.clearLayers();
    } else {
        crossLineLayerGroup.clearLayers();
    }

    let color = type === 'cross' ? '#f59e0b' : '#f43f5e';
    let weight = type === 'cross' ? 1 : 1.5;
    let dashArray = type === 'cross' ? '6, 8' : '';

    let promises = boundaryFeatures.map(boundaryFeature => {
        return new Promise((resolve, reject) => {
            let targetBoundary = (isDrone && window.droneEffectiveBoundary) ? window.droneEffectiveBoundary : boundaryFeature;
            let bbox = turf.bbox(targetBoundary);
            let center = turf.center(targetBoundary);
            let worker = new Worker('/js/gis-worker.js?v=' + (new Date().getTime() + 1000));

            worker.onmessage = function (e) {
                if (e.data.success) resolve(e.data.features);
                else reject(e.data.error);
                worker.terminate();
            };
            worker.onerror = function (e) {
                reject('Worker failed');
                worker.terminate();
            };

            let postMode = 'standard';
            let postSpacing = spacingMeters;
            let leftCount = 0;
            let rightCount = 0;

            if (mode === 'centerline' && type === 'main') {
                postMode = 'centerline_offset';
                let clSpacingEl = document.getElementById('gen-cl-spacing');
                postSpacing = clSpacingEl ? parseFloat(clSpacingEl.value) : 50;
                let clLeftEl = document.getElementById('gen-cl-left');
                leftCount = clLeftEl ? parseInt(clLeftEl.value) : 2;
                let clRightEl = document.getElementById('gen-cl-right');
                rightCount = clRightEl ? parseInt(clRightEl.value) : 2;
            }

            worker.postMessage({
                mode: postMode,
                spacingMeters: postSpacing,
                angle: angle,
                boundaryFeature: targetBoundary,
                center: center,
                bbox: bbox,
                crossSpacingMeters: 0,
                crossAngle: 0,
                centerlineFeature: centerlineFeature,
                leftCount: leftCount,
                rightCount: rightCount,
                continuous: isDrone
            });
        });
    });

    Promise.all(promises).then(results => {
        let lineIdx = 0;
        results.forEach(features => {
            features.forEach(feature => {
                let leafletLayer = L.geoJSON(feature, {
                    style: { color: color, weight: weight, opacity: 0.8, dashArray: dashArray }
                });
                leafletLayer.eachLayer(l => {
                    l.isGenerated = true;
                    l.lineType = type;
                    if (type === 'cross') {
                        crossLineLayerGroup.addLayer(l);
                    } else {
                        mainLineLayerGroup.addLayer(l);
                        lineIdx++;
                        // Add label
                        let coords = feature.geometry.coordinates;
                        if (coords && coords.length > 0) {
                            let mid = coords[Math.floor(coords.length / 2)];
                            let label = L.marker([mid[1], mid[0]], {
                                icon: L.divIcon({
                                    className: 'line-label',
                                    html: '<span style="background:rgba(26,29,35,0.85);color:#fff;padding:1px 5px;border-radius:3px;font-size:10px;font-weight:600;">L' + lineIdx + '</span>',
                                    iconSize: [30, 16]
                                })
                            });
                            labelLayerGroup.addLayer(label);
                        }
                    }
                });
            });
        });

        isGeometryStale = false;
        let warningEl = document.getElementById('stale-warning');
        if (warningEl) warningEl.classList.add('d-none');
        recalculateAllStats();
        hideMapLoading();

        // If a callback was provided, invoke it with the total main line distance and exact segment lengths
        if (typeof callback === 'function') {
            // Use the globally calculated variable if available
            let dist = (window.surveyCalcVars && window.surveyCalcVars.totalLength)
                ? window.surveyCalcVars.totalLength
                : 0;
            let lineArr = (window.surveyCalcVars && window.surveyCalcVars.mainLineLengthsArray)
                ? window.surveyCalcVars.mainLineLengthsArray
                : [];
            callback(dist, lineArr);
        }
    }).catch(err => {
        hideMapLoading();
        Swal.fire('Error', 'Line generation failed: ' + err, 'error');
    });
}

// ============================================================
// GIS DATA: EXPORT / IMPORT
// ============================================================
function exportGeoJSON() {
    let fc = { type: 'FeatureCollection', features: [] };
    drawnItems.eachLayer(function (layer) { fc.features.push(layer.toGeoJSON()); });
    mainLineLayerGroup.eachLayer(function (layer) { fc.features.push(layer.toGeoJSON()); });
    crossLineLayerGroup.eachLayer(function (layer) { fc.features.push(layer.toGeoJSON()); });
    if (fc.features.length === 0) {
        Swal.fire('No Data', 'Nothing on the map to export.', 'warning');
        return;
    }
    let dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(fc));
    let dl = document.createElement('a');
    dl.setAttribute("href", dataStr);
    dl.setAttribute("download", "survey_" + window.MAP_WORKSPACE_CONFIG.projectId + "_" + Date.now() + ".geojson");
    document.body.appendChild(dl);
    dl.click();
    dl.remove();
}

function importGeoJSON(e) {
    let file = e.target.files[0];
    if (!file) return;
    let reader = new FileReader();
    reader.onload = function (evt) {
        try {
            let geojson = JSON.parse(evt.target.result);
            drawnItems.clearLayers();
            boundaryFeatures = [];
            mainLineLayerGroup.clearLayers();
            crossLineLayerGroup.clearLayers();
            boundaryLayerGroup.clearLayers();
            labelLayerGroup.clearLayers();

            L.geoJSON(geojson, {
                style: function (feature) {
                    if (feature.geometry.type === 'Polygon' || feature.geometry.type === 'MultiPolygon') {
                        return { color: '#3b82f6', weight: 3, fillOpacity: 0.15 };
                    }
                    return { color: '#f43f5e', weight: 1.5 };
                },
                onEachFeature: function (feature, layer) {
                    drawnItems.addLayer(layer);
                    if (feature.geometry.type === 'Polygon') {
                        boundaryFeatures.push(layer.toGeoJSON());
                    }
                }
            });

            rebuildLayerGroups();
            recalculateAllStats();
            if (drawnItems.getLayers().length > 0) { map.fitBounds(drawnItems.getBounds()); }
            Swal.fire('Imported', 'GeoJSON data imported.', 'success');
        } catch (err) {
            Swal.fire('Invalid File', 'Could not parse the GeoJSON file.', 'error');
        }
        document.getElementById('file-import-geojson').value = '';
    };
    reader.readAsText(file);
}

// ============================================================
// SAVE PLANNING
// ============================================================
function saveSurveyPlanning() {
    console.log("DEBUG: --- saveSurveyPlanning() INITIATED ---");
    if (isGeometryStale) {
        console.log("DEBUG: isGeometryStale is TRUE, blocking save.");
        Swal.fire('Stale Lines', 'Parameters have changed. Please regenerate lines before saving.', 'warning');
        return;
    }

    console.log("DEBUG: Calling recalculateAllStats() from saveSurveyPlanning...");
    recalculateAllStats();
    console.log("DEBUG: Finished recalculateAllStats() without breaking.");


    let lines = { type: 'FeatureCollection', features: [] };
    function collectLine(layer, defaultType) {
        if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
            lines.features.push({
                type: 'Feature',
                geometry: layer.toGeoJSON().geometry,
                properties: {
                    line_type: layer.lineType || defaultType || 'main'
                }
            });
        }
    }
    drawnItems.eachLayer(l => collectLine(l, 'reference'));
    mainLineLayerGroup.eachLayer(l => collectLine(l, 'main'));
    crossLineLayerGroup.eachLayer(l => collectLine(l, 'cross'));

    let boundariesPayload = boundaryFeatures.map(feat => {
        let coords = (feat.geometry && feat.geometry.coordinates && feat.geometry.coordinates[0]) ? feat.geometry.coordinates[0] : [];
        return {
            geometry: feat,
            area: turf.area(feat),
            perimeter: turf.length(turf.polygonToLine(feat), { units: 'meters' }),
            vertex_count: coords.length > 0 ? coords.length - 1 : 0,
            centroid: turf.centroid(feat).geometry
        };
    });

    let mapPayload = {
        boundaries: boundariesPayload,
        lines: lines,
        generation_settings: {
            line_spacing: document.getElementById('gen-spacing') ? document.getElementById('gen-spacing').value : null,
            orientation_angle: document.getElementById('gen-angle') ? document.getElementById('gen-angle').value : null,
            cross_spacing: document.getElementById('gen-cross-spacing') ? document.getElementById('gen-cross-spacing').value : null
        },
        is_generated: true,
        override_total_distance_meters: window.currentTotalLengthMeters || 0
    };

    let droneId = document.getElementById('drone_model_select')?.value;
    let droneSpec = (window.droneMappingSpecs && window.droneMappingSpecs.drones) ? window.droneMappingSpecs.drones.find(d => d.name === droneId) : null;
    let usableFlightTime = (droneSpec && droneSpec.usable_flight_time_min > 0) ? droneSpec.usable_flight_time_min : null;

    let paramsPayload = {
        sbes: {
            survey_speed_knots: document.getElementById('sbes-speed')?.value || null,
            working_hours_per_day: document.getElementById('sbes-workhrs')?.value || null
        },
        drone: {
            drone_model: document.getElementById('drone_model_select')?.value || null,
            camera_model: document.getElementById('drone_camera_model')?.value || null,
            altitude_m: document.getElementById('drone_altitude')?.value || null,
            target_gsd_cm: document.getElementById('drone_target_gsd')?.value || null,
            gsd_cm: document.getElementById('drone_gsd_val')?.value || null,
            ground_footprint_width_m: document.getElementById('drone_ground_width_val')?.value || null,
            ground_footprint_height_m: document.getElementById('drone_ground_height_val')?.value || null,
            photo_spacing_m: document.getElementById('drone_photo_spacing_val')?.value || null,
            flight_line_spacing_m: document.getElementById('drone_line_spacing_val')?.value || null,
            photo_interval_s: document.getElementById('drone_photo_interval_val')?.value || null,
            usable_flight_time_min: usableFlightTime,
            sortie_count: document.getElementById('drone_sortie_count')?.value || null,
            speed_ms: document.getElementById('drone_speed')?.value || null,
            front_overlap_percent: document.getElementById('drone_front_overlap')?.value || null,
            side_overlap_percent: document.getElementById('drone_side_overlap')?.value || null,
            course_angle_deg: window.droneTempAngle !== undefined ? window.droneTempAngle : (document.getElementById('drone_course_angle')?.value || null),
            mapping_margin_m: document.getElementById('drone_mapping_margin')?.value || null,
            capture_mode: document.querySelector('input[name="capture_mode"]:checked') ? document.querySelector('input[name="capture_mode"]:checked').value : null,
            total_flight_distance_m: document.getElementById('drone_total_distance')?.value || null,
            total_images: document.getElementById('drone_total_images')?.value || null,
            estimated_duration_hours: document.getElementById('drone_duration_hours')?.value || null
        },
        allowances: {
            weather_days: document.getElementById('project-weather')?.value || 0,
            mod_demod_days: document.getElementById('project-mob')?.value || 0,
            patch_test_days: document.getElementById('project-patch')?.value || 0
        }
    };

    Swal.fire({
        title: 'Saving Planning...',
        text: 'Saving map geometry and parameters...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    console.log("DEBUG: POSTing to backend -> ", mapPayload);
    fetch(window.MAP_WORKSPACE_CONFIG.saveMapUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ ...mapPayload, params: paramsPayload })
    })
        .then(response => {
            console.log("DEBUG: Map POST Response received.", response);
            return response.json();
        })
        .then(mapResult => {
            console.log("DEBUG: Map POST Result JSON parsed.", mapResult);
            if (!mapResult.success) throw new Error("Map save failed: " + (mapResult.message || ''));

            console.log("DEBUG: POSTing Parameters to backend -> ", paramsPayload);
            return fetch(window.MAP_WORKSPACE_CONFIG.saveParametersUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(paramsPayload)
            });
        })
        .then(response => response.json())
        .then(paramsResult => {
            if (!paramsResult.success) throw new Error("Parameter save failed: " + (paramsResult.message || ''));

            // Success message immediately
            Swal.fire({
                icon: 'success',
                title: 'Saved',
                text: 'Survey planning data saved successfully.',
                timer: 1500,
                showConfirmButton: false
            });

            // Take screenshot silently in background before redirect
            let mapEl = document.getElementById('map');
            let controlContainer = mapEl.querySelector('.leaflet-control-container');
            if (controlContainer) controlContainer.style.display = 'none';

            let floatingOverlays = document.querySelectorAll('.workspace-map > .position-absolute');
            floatingOverlays.forEach(el => el.style.display = 'none');

            domtoimage.toPng(mapEl, {
                width: mapEl.offsetWidth,
                height: mapEl.offsetHeight,
                style: { transform: 'none' }
            }).then(dataUrl => {
                if (controlContainer) controlContainer.style.display = '';
                floatingOverlays.forEach(el => el.style.display = '');

                fetch(window.MAP_WORKSPACE_CONFIG.screenshotUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ image: dataUrl })
                }).finally(() => {
                    window.location.href = window.location.pathname + '?t=' + new Date().getTime();
                });
            }).catch(err => {
                if (controlContainer) controlContainer.style.display = '';
                floatingOverlays.forEach(el => el.style.display = '');
                // Redirect anyway even if screenshot fails
                window.location.href = window.location.pathname + '?t=' + new Date().getTime();
            });
        })
        .catch(error => {
            console.error("Save Error:", error);
            Swal.fire('Error', error.message || 'Failed to save map data.', 'error');
        });
}

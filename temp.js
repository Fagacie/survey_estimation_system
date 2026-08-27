
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
        let boundaryLayerGroup = L.layerGroup();
        let mainLineLayerGroup = L.layerGroup();
        let crossLineLayerGroup = L.layerGroup();
        let labelLayerGroup = L.layerGroup();

        // Base layers
        let baseLayers = {};
        let activeBaseLayer = null;

        function markStale() {
            let hasGeneratedLines = false;
            mainLineLayerGroup.eachLayer(l => { hasGeneratedLines = true; });
            crossLineLayerGroup.eachLayer(l => { hasGeneratedLines = true; });
            if (hasGeneratedLines) {
                isGeometryStale = true;
                document.getElementById('stale-warning').classList.remove('d-none');
            }
        }

        // ============================================================
        // INITIALIZATION
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Initialize Map
            map = L.map('map', { zoomControl: true }).setView([4.2105, 101.9758], 6);

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

            // Basemap switcher
            document.querySelectorAll('.map-basemap-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.map-basemap-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    let key = this.dataset.basemap;
                    map.removeLayer(activeBaseLayer);
                    activeBaseLayer = baseLayers[key];
                    activeBaseLayer.addTo(map);
                });
            });

            // 3. Scale bar
            L.control.scale({ position: 'bottomright', imperial: false }).addTo(map);

            // 4. Coordinate display
            map.on('mousemove', function(e) {
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

            // 7. Load Existing Data
            loadExistingData();

            // 8. Draw Events
            map.on(L.Draw.Event.CREATED, function (e) {
                var type = e.layerType, layer = e.layer;
                drawnItems.addLayer(layer);

                // Categorize drawn layer
                if (type === 'polygon') {
                    boundaryLayerGroup.addLayer(L.geoJSON(layer.toGeoJSON(), {
                        style: { color: '#3b82f6', weight: 3, fillOpacity: 0.15 }
                    }));
                }

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
            document.getElementById('btn-generate-main').addEventListener('click', () => generateLines('main'));
            document.getElementById('btn-generate-cross').addEventListener('click', () => generateLines('cross'));

            // 10. Clear lines
            document.getElementById('btn-clear-lines').addEventListener('click', function() {
                let toRemove = [];
                drawnItems.eachLayer(function(layer) {
                    if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
                        toRemove.push(layer);
                    }
                });
                toRemove.forEach(l => drawnItems.removeLayer(l));
                mainLineLayerGroup.clearLayers();
                crossLineLayerGroup.clearLayers();
                labelLayerGroup.clearLayers();
                isGeometryStale = false;
                document.getElementById('stale-warning').classList.add('d-none');
                recalculateAllStats();
            });

            // 11. Parameter change listeners
            document.getElementById('gen-mode').addEventListener('change', function() {
                if(this.value === 'centerline') {
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

            // 11b. Image Overlay Logic
            let currentImageOverlay = null;
            let overlayMarkers = [];
            
            let btnAddOverlay = document.getElementById('btn-add-overlay');
            if (btnAddOverlay) {
                btnAddOverlay.addEventListener('click', function() {
                    let fileInput = document.getElementById('file-overlay-image');
                    if (fileInput.files.length === 0) {
                        Swal.fire('No Image', 'Please select an image file first.', 'warning');
                        return;
                    }
                    
                    let file = fileInput.files[0];
                    let reader = new FileReader();
                    reader.onload = function(e) {
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
                        
                        currentImageOverlay = L.imageOverlay(imgUrl, imageBounds, {opacity: 0.7, zIndex: 1}).addTo(map);
                        
                        // Add draggable corner markers for basic georeferencing
                        let tlMarker = L.marker([imageBounds[1][0], imageBounds[0][1]], {draggable: true}).addTo(map);
                        let brMarker = L.marker([imageBounds[0][0], imageBounds[1][1]], {draggable: true}).addTo(map);
                        
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

            // 12. Time estimation reactivity
            document.getElementById('sbes-speed').addEventListener('input', calculateTimeEstimation);
            document.getElementById('sbes-workhrs').addEventListener('input', calculateTimeEstimation);
            document.getElementById('sbes-turntime').addEventListener('input', calculateTimeEstimation);
            document.getElementById('sbes-weather').addEventListener('input', calculateTimeEstimation);
            document.getElementById('sbes-mod').addEventListener('input', calculateTimeEstimation);

            // 13. Save Planning
            document.getElementById('btn-save-planning').addEventListener('click', saveSurveyPlanning);

            // 14. Layer toggle listeners
            document.getElementById('layer-boundaries').addEventListener('change', function() {
                this.checked ? boundaryLayerGroup.addTo(map) : map.removeLayer(boundaryLayerGroup);
            });
            document.getElementById('layer-main-lines').addEventListener('change', function() {
                this.checked ? mainLineLayerGroup.addTo(map) : map.removeLayer(mainLineLayerGroup);
            });
            document.getElementById('layer-cross-lines').addEventListener('change', function() {
                this.checked ? crossLineLayerGroup.addTo(map) : map.removeLayer(crossLineLayerGroup);
            });
            document.getElementById('layer-labels').addEventListener('change', function() {
                this.checked ? labelLayerGroup.addTo(map) : map.removeLayer(labelLayerGroup);
            });
        });

        // ============================================================
        // LOAD EXISTING DATA
        // ============================================================
        function loadExistingData() {
            @if($project->boundaries->count() > 0)
                var boundaries = {!! $project->boundaries->map(fn($b) => $b->geometry)->toJson() !!};
                boundaries.forEach(function(geoJsonFeature) {
                    var layer = L.geoJSON(geoJsonFeature, {
                        style: { color: '#3b82f6', weight: 3, fillOpacity: 0.15 }
                    });
                    layer.eachLayer(function (l) {
                        drawnItems.addLayer(l);
                        boundaryLayerGroup.addLayer(L.geoJSON(l.toGeoJSON(), {
                            style: { color: '#3b82f6', weight: 3, fillOpacity: 0.15 }
                        }));
                    });
                });
            @endif

            fetch("1")
                .then(response => response.json())
                .then(existingLines => {
                    if (existingLines && existingLines.length > 0) {
                        existingLines.forEach(function(geoJsonFeature) {
                            var lineType = (geoJsonFeature.properties && geoJsonFeature.properties.line_type) || 'main';
                            var isMain = lineType === 'main';

                            var color = isMain ? '#f43f5e' : '#f59e0b';
                            var lineLayer = L.geoJSON(geoJsonFeature, {
                                style: { color: color, weight: isMain ? 1.5 : 1, opacity: 0.8, dashArray: isMain ? '' : '6, 8' }
                            });

                            lineLayer.eachLayer(function (l) {
                                l.isGenerated = true;
                                l.lineType = lineType;
                                drawnItems.addLayer(l);

                                var vizLayer = L.geoJSON(l.toGeoJSON(), {
                                    style: { color: color, weight: isMain ? 1.5 : 1, opacity: 0.8, dashArray: isMain ? '' : '6, 8' }
                                });
                                if (isMain) {
                                    mainLineLayerGroup.addLayer(vizLayer);
                                } else {
                                    crossLineLayerGroup.addLayer(vizLayer);
                                }
                            });
                        });
                    }

                    recalculateAllStats();

                    if (drawnItems.getLayers().length > 0) {
                        map.fitBounds(drawnItems.getBounds());
                    } else if ("1") {
                        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent("1"))
                            .then(r => r.json())
                            .then(data => {
                                if(data && data.length > 0) {
                                    let b = data[0].boundingbox;
                                    map.fitBounds([[b[0], b[2]], [b[1], b[3]]]);
                                }
                            });
                    }
                })
                .catch(error => console.error('Error loading map lines:', error));
        }

        // ============================================================
        // REBUILD LAYER GROUPS (after edit/delete)
        // ============================================================
        function rebuildLayerGroups() {
            boundaryLayerGroup.clearLayers();
            mainLineLayerGroup.clearLayers();
            crossLineLayerGroup.clearLayers();
            labelLayerGroup.clearLayers();

            let mainIdx = 0;
            drawnItems.eachLayer(function(layer) {
                if (layer instanceof L.Polygon) {
                    boundaryLayerGroup.addLayer(L.geoJSON(layer.toGeoJSON(), {
                        style: { color: '#3b82f6', weight: 3, fillOpacity: 0.15 }
                    }));
                } else if (layer instanceof L.Polyline) {
                    let isMain = layer.isGenerated && (layer.lineType === 'main' || !layer.lineType);
                    let isCross = layer.lineType === 'cross';
                    let color = isMain ? '#f43f5e' : '#f59e0b';
                    var vizLayer = L.geoJSON(layer.toGeoJSON(), {
                        style: { color: color, weight: isMain ? 1.5 : 1, opacity: 0.8, dashArray: isMain ? '' : '6, 8' }
                    });
                    if (isCross) {
                        crossLineLayerGroup.addLayer(vizLayer);
                    } else {
                        mainLineLayerGroup.addLayer(vizLayer);
                        mainIdx++;
                        // Add label
                        let geojson = layer.toGeoJSON();
                        if (geojson.geometry && geojson.geometry.coordinates) {
                            let coords = geojson.geometry.coordinates;
                            let mid = coords[Math.floor(coords.length / 2)];
                            let label = L.marker([mid[1], mid[0]], {
                                icon: L.divIcon({
                                    className: 'line-label',
                                    html: '<span style="background:rgba(26,29,35,0.8);color:#fff;padding:1px 5px;border-radius:3px;font-size:10px;font-weight:600;">L' + mainIdx + '</span>',
                                    iconSize: [30, 16]
                                })
                            });
                            labelLayerGroup.addLayer(label);
                        }
                    }
                }
            });
        }

        // ============================================================
        // RECALCULATE ALL STATISTICS
        // RECALCULATE ALL STATISTICS
        // ============================================================
        function recalculateAllStats() {
            let mainCount = 0, crossCount = 0;
            let mainLength = 0, crossLength = 0;
            let boundaryArea = 0, boundaryPerimeter = 0, verticesCount = 0;
            boundaryFeatures = [];
            
            let mode = document.getElementById('gen-mode').value;
            let foundCenterline = false;

            drawnItems.eachLayer(function(layer) {
                var geojson = layer.toGeoJSON();

                if (layer instanceof L.Polygon) {
                    boundaryFeatures.push(geojson);
                    
                    let pArea = turf.area(geojson);
                    let pPerim = turf.length(geojson, {units: 'meters'});
                    
                    boundaryArea += pArea;
                    boundaryPerimeter += pPerim;
                    
                    // Map Tooltip: Show KM stats on hover
                    let areaKm2 = (pArea / 1000000).toLocaleString(undefined, {maximumFractionDigits:2});
                    let perimKm = (pPerim / 1000).toLocaleString(undefined, {maximumFractionDigits:2});
                    layer.bindTooltip(`Area: ${areaKm2} km²<br>Perimeter: ${perimKm} km`, {
                        permanent: false, 
                        direction: 'center',
                        className: 'bg-dark text-white border-0 shadow-sm'
                    });

                    if (geojson.geometry && geojson.geometry.coordinates && geojson.geometry.coordinates[0]) {
                        let coords = geojson.geometry.coordinates[0];
                        verticesCount += (coords.length > 0 ? coords.length - 1 : 0);
                    }
                } else if (layer instanceof L.Polyline) {
                    var len = turf.length(geojson, {units: 'meters'});
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

                    if (isCross) {
                        crossLength += len;
                        crossCount++;
                    } else if (isMain) {
                        mainLength += len;
                        mainCount++;
                    }
                    
                    layer.feature = layer.feature || { type: "Feature", properties: {} };
                    layer.feature.properties.length = len;
                    layer.feature.properties.line_type = isCross ? 'cross' : (isMain ? 'main' : 'reference');
                }
            });

            let coverageWidth = 0; // perpendicular extent
            let lineLength = 0;    // length of each individual survey line (shortest side)

            if (boundaryFeatures.length > 0) {
                let angle = parseFloat(document.getElementById('gen-angle').value) || 0;
                let fc = turf.featureCollection(boundaryFeatures);
                let center = turf.center(fc);
                let rotatedFc = turf.transformRotate(fc, -angle, {pivot: center});
                let rotBbox = turf.bbox(rotatedFc);

                let widthX  = turf.distance(turf.point([rotBbox[0], rotBbox[1]]), turf.point([rotBbox[2], rotBbox[1]]), {units: 'meters'});
                let heightY = turf.distance(turf.point([rotBbox[0], rotBbox[1]]), turf.point([rotBbox[0], rotBbox[3]]), {units: 'meters'});

                // INOS Required Logic: Survey Width (line length) MUST be the shortest side
                lineLength    = Math.min(widthX, heightY);
                coverageWidth = Math.max(widthX, heightY);

                if (document.getElementById('stat-survey-width')) {
                    document.getElementById('stat-survey-width').innerText = (lineLength / 1000).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' km';
                }
            } else {
                if (document.getElementById('stat-survey-width')) {
                    document.getElementById('stat-survey-width').innerText = '0.00 km';
                }
            }

            // FORMULA: Main Distance = Number of Main Lines × Survey Width (lineLength)
            let mainLengthMeters = mainCount * lineLength;
            
            // Crossline Distance = actual real-world length calculated (crossLength)
            let totalLength = mainLengthMeters + crossLength;

            // Expose globally for saveSurveyPlanning to use
            window.currentTotalLengthMeters = totalLength;

            // Convert distances to NM
            let mainNM = (mainLengthMeters / 1000) / 1.852;
            let crossNM = (crossLength / 1000) / 1.852;
            let totalNM = mainNM + crossNM;

            // Update Sidebar Stats - Boundary
            let areaStr = boundaryArea.toLocaleString(undefined, {maximumFractionDigits:2}) + ' m² (' + (boundaryArea / 1000000).toLocaleString(undefined, {maximumFractionDigits:2}) + ' km²)';
            let perimStr = boundaryPerimeter.toLocaleString(undefined, {maximumFractionDigits:2}) + ' m (' + (boundaryPerimeter / 1000).toLocaleString(undefined, {maximumFractionDigits:2}) + ' km)';
            
            document.getElementById('stat-boundary-area').innerText = areaStr;
            document.getElementById('stat-boundary-perimeter').innerText = perimStr;

            // Update Sidebar Stats - Survey Lines (counts + NM only)
            document.getElementById('stat-main-count').innerText  = mainCount;
            document.getElementById('stat-cross-count').innerText = crossCount;
            document.getElementById('stat-total-lines-count').innerText = totalLines;
            if (document.getElementById('stat-total-line-length')) {
                document.getElementById('stat-total-line-length').innerText = (totalLineLength/1000).toLocaleString(undefined, {maximumFractionDigits:2}) + ' km';
            }
            document.getElementById('stat-total-dist').innerText = (totalLength/1000).toLocaleString(undefined, {maximumFractionDigits:2}) + ' km';
            document.getElementById('stat-total-nm').innerText = totalNM.toLocaleString(undefined, {maximumFractionDigits:2}) + ' NM';

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
                totalNM: totalNM
            };

            // Update UI elements if they exist
            function updateIfExist(id, val) {
                let el = document.getElementById(id);
                if(el) el.innerText = val;
            }

            // Boundary
            updateIfExist('stat-boundary-area', boundaryArea.toLocaleString(undefined, {maximumFractionDigits:2}) + ' m²');
            updateIfExist('stat-boundary-perimeter', (boundaryPerimeter / 1000).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' km');
            updateIfExist('stat-survey-length', (coverageWidth / 1000).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' km');

            // Main Lines
            updateIfExist('stat-main-count', mainCount);
            updateIfExist('stat-main-dist-km', (mainLengthMeters / 1000).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' km');
            updateIfExist('stat-main-dist-nm', mainNM.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' NM');

            // Cross Lines
            updateIfExist('stat-cross-count', crossCount);
            updateIfExist('stat-cross-dist-km', (crossLength / 1000).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' km');
            updateIfExist('stat-cross-dist-nm', crossNM.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' NM');

            // Total Distance
            updateIfExist('stat-total-nm', totalNM.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' NM');

            // Update Metric Strip (always-visible top bar)
            updateIfExist('ms-total-nm', totalNM.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' NM');
            updateIfExist('ms-total-lines', (mainCount + crossCount));

            calculateTimeEstimation();
        }

        // ============================================================
        // TIME ESTIMATION
        // ============================================================
        function calculateTimeEstimation() {
            let speedKnots  = parseFloat(document.getElementById('sbes-speed').value)   || 0;
            let workHrs     = parseFloat(document.getElementById('sbes-workhrs').value)  || 0;
            let weatherDays = parseFloat(document.getElementById('sbes-weather').value)  || 0;
            let modDays     = parseFloat(document.getElementById('sbes-mod').value)       || 0;
            let patchDays   = parseFloat(document.getElementById('sbes-patch').value)     || 0;

            let totalNm = window.surveyCalcVars ? window.surveyCalcVars.totalNM : 0;
            let workingDays = 0;
            let surveyHours = 0;

            if (speedKnots > 0 && totalNm > 0) {
                // FORMULA: Survey Time (hours) = Total Survey Distance (NM) / Survey Speed (knots)
                surveyHours = totalNm / speedKnots;
                
                let elHours = document.getElementById('calc-survey-hours');
                if(elHours) elHours.innerText = surveyHours.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' hours';
                
                let msHours = document.getElementById('ms-survey-hours');
                if(msHours) msHours.innerText = surveyHours.toLocaleString(undefined, {minimumFractionDigits:1, maximumFractionDigits:1});

                if (workHrs > 0) {
                    // FORMULA: Survey Days = Total Survey Time (hours) / Working Hours Per Day (Exact fractional days)
                    workingDays = surveyHours / workHrs;
                    let elWorkingDays = document.getElementById('calc-working-days');
                    if(elWorkingDays) elWorkingDays.innerText = workingDays.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' days';
                } else {
                    let elWorkingDays = document.getElementById('calc-working-days');
                    if(elWorkingDays) elWorkingDays.innerText = '0.00 days';
                }
            } else {
                let elHours = document.getElementById('calc-survey-hours');
                if(elHours) elHours.innerText = '0.00 hours';
                let elWorkingDays = document.getElementById('calc-working-days');
                if(elWorkingDays) elWorkingDays.innerText = '0.00 days';
                let msHours = document.getElementById('ms-survey-hours');
                if(msHours) msHours.innerText = '0.0';
            }

            // FORMULA: Estimated Project Duration = Survey Days + Weather Allowance + MOB/DEMOB + Other (Patch Test)
            let totalDays = workingDays + weatherDays + modDays + patchDays;
            
            let elTotalDays = document.getElementById('calc-total-days');
            if(elTotalDays) elTotalDays.innerText = totalDays.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}) + ' days';
            
            let msDays = document.getElementById('ms-working-days');
            if(msDays) msDays.innerText = totalDays.toLocaleString(undefined, {minimumFractionDigits:1, maximumFractionDigits:1});
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
        function generateLines(type) {
            let mode = document.getElementById('gen-mode').value;
            let centerlineFeature = null;

            if (mode === 'centerline' && type === 'main') {
                drawnItems.eachLayer(function(layer) {
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

            if (type === 'cross') {
                spacingMeters = parseFloat(document.getElementById('gen-cross-spacing').value);
                let baseAngle = parseFloat(document.getElementById('gen-angle').value) || 0;
                // Hardcode tie lines to strictly 90 degrees offset from main lines
                let crossAngle = 90;
                angle = baseAngle + crossAngle;

                if (!spacingMeters || spacingMeters <= 0) {
                    Swal.fire('Missing Input', 'Please enter a Cross Line spacing value.', 'warning');
                    return;
                }
            } else {
                spacingMeters = parseFloat(document.getElementById('gen-spacing').value);
                angle = parseFloat(document.getElementById('gen-angle').value);
            }

            let loadingText = type === 'cross' ? 'Generating Tie Lines...' : 'Generating Main Lines...';
            showMapLoading(loadingText);

            // Clear existing lines of this type
            let toRemove = [];
            drawnItems.eachLayer(function(layer) {
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
                    let bbox = turf.bbox(boundaryFeature);
                    let center = turf.center(boundaryFeature);
                    let worker = new Worker('/js/gis-worker.js?v=' + (new Date().getTime() + 1000));

                    worker.onmessage = function(e) {
                        if (e.data.success) resolve(e.data.features);
                        else reject(e.data.error);
                        worker.terminate();
                    };
                    worker.onerror = function(e) {
                        reject('Worker failed');
                        worker.terminate();
                    };

                    let postMode = 'standard';
                    let postSpacing = spacingMeters;
                    let leftCount = 0;
                    let rightCount = 0;

                    if (mode === 'centerline' && type === 'main') {
                        postMode = 'centerline_offset';
                        postSpacing = parseFloat(document.getElementById('gen-cl-spacing').value);
                        leftCount = parseInt(document.getElementById('gen-cl-left').value);
                        rightCount = parseInt(document.getElementById('gen-cl-right').value);
                    }

                    worker.postMessage({
                        mode: postMode,
                        spacingMeters: postSpacing,
                        angle: angle,
                        boundaryFeature: boundaryFeature,
                        center: center,
                        bbox: bbox,
                        crossSpacingMeters: 0,
                        crossAngle: 0,
                        centerlineFeature: centerlineFeature,
                        leftCount: leftCount,
                        rightCount: rightCount
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
                            drawnItems.addLayer(l);

                            let vizLayer = L.geoJSON(l.toGeoJSON(), {
                                style: { color: color, weight: weight, opacity: 0.8, dashArray: dashArray }
                            });
                            if (type === 'cross') {
                                crossLineLayerGroup.addLayer(vizLayer);
                            } else {
                                mainLineLayerGroup.addLayer(vizLayer);
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
                document.getElementById('stale-warning').classList.add('d-none');
                recalculateAllStats();
                hideMapLoading();
            }).catch(err => {
                hideMapLoading();
                Swal.fire('Error', 'Line generation failed: ' + err, 'error');
            });
        }

        // ============================================================
        // GIS DATA: EXPORT / IMPORT
        // ============================================================
        function exportGeoJSON() {
            if (drawnItems.getLayers().length === 0) {
                Swal.fire('No Data', 'Nothing on the map to export.', 'warning');
                return;
            }
            let fc = { type: 'FeatureCollection', features: [] };
            drawnItems.eachLayer(function(layer) { fc.features.push(layer.toGeoJSON()); });
            let dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(fc));
            let dl = document.createElement('a');
            dl.setAttribute("href", dataStr);
            dl.setAttribute("download", "survey_" + 1 + "_" + Date.now() + ".geojson");
            document.body.appendChild(dl);
            dl.click();
            dl.remove();
        }

        function importGeoJSON(e) {
            let file = e.target.files[0];
            if (!file) return;
            let reader = new FileReader();
            reader.onload = function(evt) {
                try {
                    let geojson = JSON.parse(evt.target.result);
                    drawnItems.clearLayers();
                    boundaryFeatures = [];
                    mainLineLayerGroup.clearLayers();
                    crossLineLayerGroup.clearLayers();
                    boundaryLayerGroup.clearLayers();
                    labelLayerGroup.clearLayers();

                    L.geoJSON(geojson, {
                        style: function(feature) {
                            if (feature.geometry.type === 'Polygon' || feature.geometry.type === 'MultiPolygon') {
                                return { color: '#3b82f6', weight: 3, fillOpacity: 0.15 };
                            }
                            return { color: '#f43f5e', weight: 1.5 };
                        },
                        onEachFeature: function(feature, layer) {
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
            if (isGeometryStale) {
                Swal.fire('Stale Lines', 'Parameters have changed. Please regenerate lines before saving.', 'warning');
                return;
            }

            recalculateAllStats();


            let lines = { type: 'FeatureCollection', features: [] };
            drawnItems.eachLayer(function(layer) {
                if (layer instanceof L.Polyline && !(layer instanceof L.Polygon)) {
                    lines.features.push(layer.toGeoJSON());
                }
            });

            let boundariesPayload = boundaryFeatures.map(feat => {
                let coords = (feat.geometry && feat.geometry.coordinates && feat.geometry.coordinates[0]) ? feat.geometry.coordinates[0] : [];
                return {
                    geometry: feat,
                    area: turf.area(feat),
                    perimeter: turf.length(feat, {units: 'meters'}),
                    vertex_count: coords.length > 0 ? coords.length - 1 : 0,
                    centroid: turf.centroid(feat).geometry
                };
            });

            let mapPayload = {
                boundaries: boundariesPayload,
                lines: lines,
                generation_settings: {
                    line_spacing: document.getElementById('gen-spacing').value,
                    orientation_angle: document.getElementById('gen-angle').value,
                    cross_spacing: document.getElementById('gen-cross-spacing').value || null
                },
                is_generated: true,
                override_total_distance_meters: window.currentTotalLengthMeters || 0
            };

            let paramsPayload = {
                sbes: {
                    survey_speed_knots: document.getElementById('sbes-speed').value || null,
                    working_hours_per_day: document.getElementById('sbes-workhrs').value || null,
                    weather_days: document.getElementById('sbes-weather').value || 0,
                    mod_demod_days: document.getElementById('sbes-mod').value || 0,
                    patch_test_days: document.getElementById('sbes-patch').value || 0
                }
            };

            Swal.fire({
                title: 'Saving Planning...',
                text: 'Persisting map geometry and parameters...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            fetch("1", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(mapPayload)
            })
            .then(response => response.json())
            .then(mapResult => {
                if (!mapResult.success) throw new Error("Map save failed: " + (mapResult.message || ''));

                return fetch("1", {
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

                Swal.fire({
                    icon: 'success',
                    title: 'Saved',
                    text: 'Planning data saved successfully.'
                }).then(() => window.location.reload());
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', error.message || 'Failed to save.', 'error');
            });
        }
    
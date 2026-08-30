<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map Capture - {{ $location->name }}</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; background: #f8fafc; }
        #map { width: 1200px; height: 800px; }
        .leaflet-control-container { display: none !important; }
    </style>
</head>
<body>
    <div id="map"></div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var map = L.map('map', {
                zoomControl: false,
                attributionControl: false
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
            }).addTo(map);

            var boundariesData = {!! $boundaries !!};
            var linesData = {!! $lines !!};
            
            var drawnItems = new L.FeatureGroup();

            // Render boundaries
            if (boundariesData.features && boundariesData.features.length > 0) {
                L.geoJSON(boundariesData, {
                    style: function (feature) {
                        return { color: '#0f172a', weight: 3, fillOpacity: 0.1 };
                    }
                }).addTo(drawnItems);
            }

            // Render lines
            if (linesData.features && linesData.features.length > 0) {
                L.geoJSON(linesData, {
                    style: function (feature) {
                        var type = feature.properties.line_type || 'main';
                        if (type === 'cross') return { color: '#f59e0b', weight: 2, dashArray: '5, 5' };
                        return { color: '#3b82f6', weight: 2 };
                    }
                }).addTo(drawnItems);
            }

            drawnItems.addTo(map);
            
            if (drawnItems.getLayers().length > 0) {
                map.fitBounds(drawnItems.getBounds(), { padding: [50, 50] });
            } else {
                map.setView([5.3, 103.1], 10); // fallback
            }

            // Tell puppeteer we are ready
            setTimeout(function() {
                var div = document.createElement('div');
                div.id = 'map-ready';
                document.body.appendChild(div);
            }, 1000);
        });
    </script>
</body>
</html>

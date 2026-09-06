function calculateTrueMetricBounds(fc, angle, turf, map) {
    let center = turf.center(fc);
    let minX = Infinity, maxX = -Infinity;
    let minY = Infinity, maxY = -Infinity;
    let hasCoords = false;

    // Create a local metric Cartesian grid using Azimuthal Equidistant projection
    // Uses Leaflet's map.distance (Vincenty ellipsoid) for maximum precision
    turf.coordEach(fc, function (currentCoord) {
        hasCoords = true;
        let pt = turf.point(currentCoord);
        
        let pCenter = L.latLng(center.geometry.coordinates[1], center.geometry.coordinates[0]);
        let pTarget = L.latLng(currentCoord[1], currentCoord[0]);
        let d = map.distance(pCenter, pTarget);
        
        let b = turf.bearing(center, pt);
        
        let angleDiff = b - angle;
        let rad = angleDiff * Math.PI / 180;
        
        let x = d * Math.sin(rad);
        let y = d * Math.cos(rad);

        if (x < minX) minX = x;
        if (x > maxX) maxX = x;
        if (y < minY) minY = y;
        if (y > maxY) maxY = y;
    });

    if (!hasCoords) return { widthX: 0, heightY: 0, hasCoords: false };

    return {
        widthX: Math.abs(maxX - minX),
        heightY: Math.abs(maxY - minY),
        hasCoords: true
    };
}
window.calculateTrueMetricBounds = calculateTrueMetricBounds;

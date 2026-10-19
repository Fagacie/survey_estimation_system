function calculateTrueMetricBounds(fc, angle, turf, map) {
    if (!fc || !turf) return { widthX: 0, heightY: 0, hasCoords: false };

    try {
        let rotatedFc = turf.transformRotate(fc, -(angle || 0));
        let rotBbox = turf.bbox(rotatedFc);

        let widthX = turf.distance(turf.point([rotBbox[0], rotBbox[1]]), turf.point([rotBbox[2], rotBbox[1]]), { units: 'meters' });
        let heightY = turf.distance(turf.point([rotBbox[0], rotBbox[1]]), turf.point([rotBbox[0], rotBbox[3]]), { units: 'meters' });

        return {
            widthX: widthX,
            heightY: heightY,
            hasCoords: true
        };
    } catch (e) {
        console.warn('calculateTrueMetricBounds fallback:', e);
        return { widthX: 0, heightY: 0, hasCoords: false };
    }
}
window.calculateTrueMetricBounds = calculateTrueMetricBounds;

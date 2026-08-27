// public/js/gis-worker.js
importScripts('https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js');

self.addEventListener('message', function(e) {
    const data = e.data;
    const { 
        spacingMeters, angle, boundaryFeature, center, bbox, 
        crossSpacingMeters, crossAngle, 
        mode, centerlineFeature 
    } = data;

    let linesFeatures = [];

    try {
        if (mode === 'centerline_offset' && centerlineFeature) {
            linesFeatures = generateCenterlineOffsets(spacingMeters, centerlineFeature, boundaryFeature, data.leftCount, data.rightCount);
        } else if (mode === 'centerline' && centerlineFeature) {
            linesFeatures = generateCrossSections(spacingMeters, centerlineFeature, boundaryFeature);
        } else {
            // Generate Main Lines (Discrete)
            if (spacingMeters > 0) {
                let generated = generateLineSet(spacingMeters, angle, boundaryFeature, center, bbox, false);
                linesFeatures = linesFeatures.concat(generated);
            }

            // Generate Cross Lines (Tie Lines) - typically not continuous
            if (crossSpacingMeters > 0) {
                let crossGenerated = generateLineSet(crossSpacingMeters, angle + crossAngle, boundaryFeature, center, bbox, false);
                linesFeatures = linesFeatures.concat(crossGenerated);
            }
        }

        self.postMessage({ success: true, features: linesFeatures });
    } catch (err) {
        self.postMessage({ success: false, error: err.message });
    }
});

function generateLineSet(spacingMeters, angle, boundaryFeature, center, bbox, continuous) {
    if (spacingMeters <= 0) return [];

    let rawSegments = [];
    
    // 1. Rotate boundary backward so we can calculate a true perpendicular grid width
    let rotatedBoundary = turf.transformRotate(boundaryFeature, -angle, {pivot: center});
    let rotBbox = turf.bbox(rotatedBoundary);
    
    let minX = rotBbox[0];
    let minY = rotBbox[1];
    let maxX = rotBbox[2];
    let maxY = rotBbox[3];
    
    // Distance across the polygon width
    let distanceAcross = turf.distance(turf.point([minX, minY]), turf.point([maxX, minY]), {units: 'meters'});
    let numLines = Math.floor(distanceAcross / spacingMeters) + 1;

    for(let i=0; i<numLines; i++) {
        let offset = (spacingMeters / 2) + (i * spacingMeters);
        if (offset > distanceAcross) break;

        // 2. Generate vertical lines from Left to Right
        let topNode = turf.destination(turf.point([minX, maxY + 0.5]), offset, 90, {units: 'meters'});
        let bottomNode = turf.destination(turf.point([minX, minY - 0.5]), offset, 90, {units: 'meters'});
        
        let lineString = turf.lineString([topNode.geometry.coordinates, bottomNode.geometry.coordinates]);
        
        // 3. Rotate the line forward to the desired angle
        lineString = turf.transformRotate(lineString, angle, {pivot: center});

        let segmentsForThisLine = [];

        try {
            let splitLines = turf.lineSplit(lineString, boundaryFeature);
            if (splitLines.features.length > 0) {
                splitLines.features.forEach(segment => {
                    let coords = segment.geometry.coordinates;
                    let midpoint = turf.midpoint(turf.point(coords[0]), turf.point(coords[coords.length - 1]));
                    if (turf.booleanPointInPolygon(midpoint, boundaryFeature)) {
                        segmentsForThisLine.push(segment);
                    }
                });
            } else {
                let midpoint = turf.midpoint(turf.point(lineString.geometry.coordinates[0]), turf.point(lineString.geometry.coordinates[1]));
                if (turf.booleanPointInPolygon(midpoint, boundaryFeature)) {
                    segmentsForThisLine.push(lineString);
                }
            }
        } catch (e) {
            // Ignore clip errors
        }

        let filteredSegments = segmentsForThisLine.filter(segment => {
            let len = turf.length(segment, {units: 'meters'});
            return len >= (spacingMeters * 0.05); // Discard lines shorter than 5% of spacing
        });

        if (filteredSegments.length > 0) {
            rawSegments.push(filteredSegments);
        }
    }

    if (!continuous) {
        let results = [];
        rawSegments.forEach(segments => {
            results = results.concat(segments);
        });
        return results;
    }

    // Combine into continuous path
    let results = [];
    let currentContinuousCoords = [];
    let reverse = false;

    for (let i = 0; i < rawSegments.length; i++) {
        let segments = rawSegments[i];
        
        // If a line is split into multiple segments due to a complex polygon, 
        // a true continuous path might be broken. For simplicity, we connect 
        // all segments in this column, but this might cross outside the boundary.
        // In professional surveying, they usually lift the sensor or just sail through.
        // We will just connect the bounding ends of the outermost segments for this column to keep it simple,
        // or just connect them all.
        
        // Sort segments in this column along the Y axis equivalent to ensure consistent direction
        // Actually, just extracting all coordinates and reversing if needed is enough.
        let columnCoords = [];
        segments.forEach(seg => {
            columnCoords = columnCoords.concat(seg.geometry.coordinates);
        });

        if (reverse) {
            columnCoords.reverse();
        }

        currentContinuousCoords = currentContinuousCoords.concat(columnCoords);
        reverse = !reverse;
    }

    if (currentContinuousCoords.length > 1) {
        results.push(turf.lineString(currentContinuousCoords));
    }

    return results;
}

function generateCrossSections(spacingMeters, centerlineFeature, boundaryFeature) {
    let results = [];
    let lineLength = turf.length(centerlineFeature, {units: 'meters'});
    
    // Generate perpendicular lines along the centerline
    for (let d = 0; d <= lineLength; d += spacingMeters) {
        let p1, p2;
        
        if (d === lineLength || (d + 1) >= lineLength) {
            // If at the end, use the previous meter to get bearing
            p1 = turf.along(centerlineFeature, Math.max(0, lineLength - 1), {units: 'meters'});
            p2 = turf.along(centerlineFeature, lineLength, {units: 'meters'});
        } else {
            p1 = turf.along(centerlineFeature, d, {units: 'meters'});
            p2 = turf.along(centerlineFeature, d + 1, {units: 'meters'});
        }
        
        let bearing = turf.bearing(p1, p2);
        
        // Actually, if we used previous meter, we should still originate the cross line from 'd' (the end of the line)
        let originPoint = turf.along(centerlineFeature, d, {units: 'meters'});
        
        // Create a line perpendicular to the bearing
        let ptA = turf.destination(originPoint, 5000, bearing + 90, {units: 'meters'});
        let ptB = turf.destination(originPoint, 5000, bearing - 90, {units: 'meters'});
        let crossLine = turf.lineString([ptA.geometry.coordinates, ptB.geometry.coordinates]);
        
        if (boundaryFeature) {
            try {
                let splitLines = turf.lineSplit(crossLine, boundaryFeature);
                if (splitLines.features.length > 0) {
                    splitLines.features.forEach(segment => {
                        let coords = segment.geometry.coordinates;
                        let midpoint = turf.midpoint(turf.point(coords[0]), turf.point(coords[coords.length - 1]));
                        if (turf.booleanPointInPolygon(midpoint, boundaryFeature)) {
                            results.push(segment);
                        }
                    });
                }
            } catch (e) { }
        } else {
            // If no boundary, just use a fixed 100m cross section
            let ptA_short = turf.destination(p1, 50, bearing + 90, {units: 'meters'});
            let ptB_short = turf.destination(p1, 50, bearing - 90, {units: 'meters'});
            results.push(turf.lineString([ptA_short.geometry.coordinates, ptB_short.geometry.coordinates]));
        }
    }
    
    return results;
}

function generateCenterlineOffsets(spacingMeters, centerlineFeature, boundaryFeature, leftCount, rightCount) {
    let results = [];
    
    // 1. Extend the centerline infinitely (5000m) in both directions so it always reaches the boundary
    let coords = centerlineFeature.geometry.coordinates;
    let extendedCoords = [...coords];
    if (coords.length >= 2) {
        let bearingFirst = turf.bearing(turf.point(coords[1]), turf.point(coords[0]));
        let pFirstExt = turf.destination(turf.point(coords[0]), 5000, bearingFirst, {units: 'meters'});
        
        let bearingLast = turf.bearing(turf.point(coords[coords.length-2]), turf.point(coords[coords.length-1]));
        let pLastExt = turf.destination(turf.point(coords[coords.length-1]), 5000, bearingLast, {units: 'meters'});
        
        extendedCoords = [pFirstExt.geometry.coordinates, ...coords, pLastExt.geometry.coordinates];
    }
    let extendedCenterline = turf.lineString(extendedCoords);

    let linesToProcess = [];
    
    // 2. Generate Left lines (farthest left to nearest left) to ensure sequential line numbering (L1, L2, L3...)
    for (let i = leftCount; i >= 1; i--) {
        try {
            // turf.lineOffset uses negative for left (or depends on direction, usually negative is left)
            let offsetLine = turf.lineOffset(extendedCenterline, -(spacingMeters * i), {units: 'meters'});
            linesToProcess.push(offsetLine);
        } catch(e) {}
    }
    
    // 3. Add Centerline
    linesToProcess.push(extendedCenterline);
    
    // 4. Generate Right lines (nearest right to farthest right)
    for (let i = 1; i <= rightCount; i++) {
        try {
            let offsetLine = turf.lineOffset(extendedCenterline, spacingMeters * i, {units: 'meters'});
            linesToProcess.push(offsetLine);
        } catch(e) {}
    }

    // Clip them to boundary if boundary exists
    linesToProcess.forEach(line => {
        if (boundaryFeature) {
            try {
                let splitLines = turf.lineSplit(line, boundaryFeature);
                if (splitLines.features.length > 0) {
                    splitLines.features.forEach(segment => {
                        let coords = segment.geometry.coordinates;
                        let midpoint = turf.midpoint(turf.point(coords[0]), turf.point(coords[coords.length - 1]));
                        if (turf.booleanPointInPolygon(midpoint, boundaryFeature)) {
                            results.push(segment);
                        }
                    });
                } else {
                    let coords = line.geometry.coordinates;
                    let midpoint = turf.midpoint(turf.point(coords[0]), turf.point(coords[coords.length - 1]));
                    if (turf.booleanPointInPolygon(midpoint, boundaryFeature)) {
                        results.push(line);
                    }
                }
            } catch (e) { }
        } else {
            results.push(line);
        }
    });

    return results;
}

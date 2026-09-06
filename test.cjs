const turf = require('./turf.js');
const fc = turf.featureCollection([turf.polygon([[[11.836, 8.5], [11.9, 8.5], [11.9, 8.5303], [11.836, 8.5303], [11.836, 8.5]]])]);
let angle = 90;
let center = turf.center(fc);

// OLD TURF METHOD
let rotatedFc = turf.transformRotate(fc, -angle, {pivot: center});
let rotBbox = turf.bbox(rotatedFc);
let widthX_old  = turf.distance(turf.point([rotBbox[0], rotBbox[1]]), turf.point([rotBbox[2], rotBbox[1]]), {units: 'meters'});
let heightY_old = turf.distance(turf.point([rotBbox[0], rotBbox[1]]), turf.point([rotBbox[0], rotBbox[3]]), {units: 'meters'});
console.log('OLD METHOD:', widthX_old, heightY_old);

// NEW PROJECTION METHOD
let minX = Infinity, maxX = -Infinity;
let minY = Infinity, maxY = -Infinity;
turf.coordEach(fc, function (currentCoord) {
    let pt = turf.point(currentCoord);
    let d = turf.distance(center, pt, {units: 'meters'});
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
console.log('NEW METHOD:', Math.abs(maxX - minX), Math.abs(maxY - minX));
console.log('TRUE DISTANCE:', turf.distance(turf.point([11.836, 8.5303]), turf.point([11.9, 8.5303]), {units: 'meters'}));

// public/js/drone_mapping/photogrammetry.js

/**
 * Calculates the Ground Sample Distance (GSD) in cm/pixel.
 * @param {number} altitude - Flight altitude in meters
 * @param {Object} cameraSpec - Camera specification object
 * @returns {number} GSD in cm/pixel
 */
function calculateGSD(altitude, cameraSpec) {
    if (!altitude || !cameraSpec || !cameraSpec.focal_length_mm || !cameraSpec.image_width_px) return 0;
    
    // GSD (m/px) = (Altitude_m * SensorWidth_mm) / (FocalLength_mm * ImageWidth_px)
    let gsdMeters = (altitude * cameraSpec.sensor_width_mm) / (cameraSpec.focal_length_mm * cameraSpec.image_width_px);
    
    // Convert to cm/px
    return gsdMeters * 100;
}

/**
 * Calculates the Ground Footprint (width and height on the ground in meters)
 * @param {number} altitude - Flight altitude in meters
 * @param {Object} cameraSpec - Camera specification object
 * @returns {Object} { ground_width_m, ground_height_m }
 */
function calculateGroundFootprint(altitude, cameraSpec) {
    if (!altitude || !cameraSpec) return { ground_width_m: 0, ground_height_m: 0 };

    let groundWidth = (altitude * cameraSpec.sensor_width_mm) / cameraSpec.focal_length_mm;
    let groundHeight = (altitude * cameraSpec.sensor_height_mm) / cameraSpec.focal_length_mm;

    return {
        ground_width_m: groundWidth,
        ground_height_m: groundHeight
    };
}

/**
 * Calculates the distance between flight lines (Side Lap spacing)
 * @param {number} groundWidth - Width of the photo footprint on the ground (meters)
 * @param {number} sideOverlapPercent - Percentage of side overlap (e.g., 70 for 70%)
 * @returns {number} Line spacing in meters
 */
function calculateLineSpacing(groundWidth, sideOverlapPercent) {
    let overlapRatio = sideOverlapPercent / 100.0;
    return groundWidth * (1 - overlapRatio);
}

/**
 * Calculates the distance between photos along a single line (Front Lap spacing)
 * @param {number} groundHeight - Height of the photo footprint on the ground (meters)
 * @param {number} frontOverlapPercent - Percentage of front overlap (e.g., 80 for 80%)
 * @returns {number} Shot spacing in meters
 */
function calculateShotSpacing(groundHeight, frontOverlapPercent) {
    let overlapRatio = frontOverlapPercent / 100.0;
    return groundHeight * (1 - overlapRatio);
}

/**
 * Expose for frontend or module usage
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        calculateGSD,
        calculateGroundFootprint,
        calculateLineSpacing,
        calculateShotSpacing
    };
} else {
    window.PhotogrammetryMath = {
        calculateGSD,
        calculateGroundFootprint,
        calculateLineSpacing,
        calculateShotSpacing
    };
}

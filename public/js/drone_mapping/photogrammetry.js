// Pure photogrammetry calculations shared by the Drone Mapping planning UI.

function isPositiveFinite(value) {
    const number = Number(value);
    return Number.isFinite(number) && number > 0;
}

/** Ground Sample Distance in cm/pixel. */
function calculateGSD(altitude, cameraSpec) {
    if (!isPositiveFinite(altitude) || !cameraSpec ||
        !isPositiveFinite(cameraSpec.sensor_width_mm) ||
        !isPositiveFinite(cameraSpec.focal_length_mm) ||
        !isPositiveFinite(cameraSpec.image_width_px)) {
        return 0;
    }

    const gsdMeters = (Number(altitude) * Number(cameraSpec.sensor_width_mm)) /
        (Number(cameraSpec.focal_length_mm) * Number(cameraSpec.image_width_px));

    return Number.isFinite(gsdMeters) ? gsdMeters * 100 : 0;
}

/** Ground footprint dimensions in metres. */
function calculateGroundFootprint(altitude, cameraSpec) {
    if (!isPositiveFinite(altitude) || !cameraSpec ||
        !isPositiveFinite(cameraSpec.sensor_width_mm) ||
        !isPositiveFinite(cameraSpec.sensor_height_mm) ||
        !isPositiveFinite(cameraSpec.focal_length_mm)) {
        return { ground_width_m: 0, ground_height_m: 0 };
    }

    const focalLength = Number(cameraSpec.focal_length_mm);
    const groundWidth = (Number(altitude) * Number(cameraSpec.sensor_width_mm)) / focalLength;
    const groundHeight = (Number(altitude) * Number(cameraSpec.sensor_height_mm)) / focalLength;

    if (!Number.isFinite(groundWidth) || !Number.isFinite(groundHeight)) {
        return { ground_width_m: 0, ground_height_m: 0 };
    }

    return { ground_width_m: groundWidth, ground_height_m: groundHeight };
}

/** Distance between flight lines based on side overlap percentage. */
function calculateLineSpacing(groundWidth, sideOverlapPercent) {
    if (!Number.isFinite(Number(groundWidth)) || Number(groundWidth) <= 0 ||
        !Number.isFinite(Number(sideOverlapPercent)) || Number(sideOverlapPercent) < 0 || Number(sideOverlapPercent) >= 100) {
        return 0;
    }

    return Number(groundWidth) * (1 - Number(sideOverlapPercent) / 100);
}

/** Distance between photo captures based on front overlap percentage. */
function calculateShotSpacing(groundHeight, frontOverlapPercent) {
    if (!Number.isFinite(Number(groundHeight)) || Number(groundHeight) <= 0 ||
        !Number.isFinite(Number(frontOverlapPercent)) || Number(frontOverlapPercent) < 0 || Number(frontOverlapPercent) >= 100) {
        return 0;
    }

    return Number(groundHeight) * (1 - Number(frontOverlapPercent) / 100);
}

/** Required altitude in metres for a target GSD in cm/pixel. */
function calculateAltitudeFromGSD(targetGSD_cm, cameraSpec) {
    if (!isPositiveFinite(targetGSD_cm) || !cameraSpec ||
        !isPositiveFinite(cameraSpec.sensor_width_mm) ||
        !isPositiveFinite(cameraSpec.focal_length_mm) ||
        !isPositiveFinite(cameraSpec.image_width_px)) {
        return 0;
    }

    const altitudeMeters = (Number(cameraSpec.image_width_px) * Number(targetGSD_cm) * Number(cameraSpec.focal_length_mm)) /
        (Number(cameraSpec.sensor_width_mm) * 100);

    return Number.isFinite(altitudeMeters) ? altitudeMeters : 0;
}

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        calculateGSD,
        calculateAltitudeFromGSD,
        calculateGroundFootprint,
        calculateLineSpacing,
        calculateShotSpacing
    };
} else {
    window.PhotogrammetryMath = {
        calculateGSD,
        calculateAltitudeFromGSD,
        calculateGroundFootprint,
        calculateLineSpacing,
        calculateShotSpacing
    };
}

// public/js/drone_mapping/camera_specs.js

/**
 * Standard Drone Camera Specifications used for Photogrammetry
 * Sensor dimensions are in millimeters (mm)
 * Focal lengths are in millimeters (mm)
 * Image dimensions are in pixels (px)
 */
const DRONE_CAMERAS = {
    'DJI_ZENMUSE_P1_24MM': {
        name: 'DJI Zenmuse P1 (24mm)',
        sensor_width_mm: 35.9,
        sensor_height_mm: 24.0,
        focal_length_mm: 24.0,
        image_width_px: 8192,
        image_height_px: 5460
    },
    'DJI_ZENMUSE_P1_35MM': {
        name: 'DJI Zenmuse P1 (35mm)',
        sensor_width_mm: 35.9,
        sensor_height_mm: 24.0,
        focal_length_mm: 35.0,
        image_width_px: 8192,
        image_height_px: 5460
    },
    'DJI_MAVIC_3_ENTERPRISE': {
        name: 'DJI Mavic 3 Enterprise',
        sensor_width_mm: 17.3, // 4/3 CMOS
        sensor_height_mm: 13.0,
        focal_length_mm: 12.29, // 24mm equivalent
        image_width_px: 5280,
        image_height_px: 3956
    },
    'DJI_ZENMUSE_H20T': {
        name: 'DJI Zenmuse H20/H20T (Wide)',
        sensor_width_mm: 6.17, // 1/2.3" CMOS
        sensor_height_mm: 4.55,
        focal_length_mm: 4.5, // 24mm eq
        image_width_px: 4056,
        image_height_px: 3040
    },
    'DJI_ZENMUSE_L1': {
        name: 'DJI Zenmuse L1 (RGB)',
        sensor_width_mm: 13.2, // 1" CMOS
        sensor_height_mm: 8.8,
        focal_length_mm: 8.8, // 24mm eq
        image_width_px: 5472,
        image_height_px: 3648
    }
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = { DRONE_CAMERAS };
} else {
    window.DRONE_CAMERAS = DRONE_CAMERAS;
}

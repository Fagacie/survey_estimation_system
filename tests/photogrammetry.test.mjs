import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const context = { window: {} };
vm.runInNewContext(readFileSync(new URL('../public/js/drone_mapping/photogrammetry.js', import.meta.url), 'utf8'), context);
const math = context.window.PhotogrammetryMath;
const camera = {
    sensor_width_mm: 13.2,
    sensor_height_mm: 8.8,
    focal_length_mm: 8.8,
    image_width_px: 5472,
    image_height_px: 3648,
};

const near = (actual, expected, tolerance = 1e-9) => assert.ok(Math.abs(actual - expected) <= tolerance, `${actual} is not within ${tolerance} of ${expected}`);

test('GSD uses altitude, sensor width, focal length, and image width', () => {
    near(math.calculateGSD(100, camera), (100 * 13.2 / (8.8 * 5472)) * 100, 1e-12);
});

test('ground footprint returns width and height in metres', () => {
    const footprint = math.calculateGroundFootprint(100, camera);
    near(footprint.ground_width_m, 150);
    near(footprint.ground_height_m, 100);
});

test('overlap percentages produce expected line and photo spacing', () => {
    near(math.calculateLineSpacing(150, 70), 45);
    near(math.calculateShotSpacing(100, 80), 20);
});

test('altitude-from-GSD reverses the GSD calculation', () => {
    const target = math.calculateGSD(100, camera);
    near(math.calculateAltitudeFromGSD(target, camera), 100, 1e-10);
});

test('invalid camera specifications return finite zero values', () => {
    assert.equal(math.calculateGSD(100, { ...camera, sensor_width_mm: 0 }), 0);
    assert.deepEqual({ ...math.calculateGroundFootprint(100, { ...camera, focal_length_mm: 0 }) }, { ground_width_m: 0, ground_height_m: 0 });
    assert.equal(math.calculateAltitudeFromGSD(2, { ...camera, image_width_px: 0 }), 0);
    assert.equal(math.calculateLineSpacing(150, 100), 0);
    assert.equal(math.calculateShotSpacing(100, 100), 0);
    assert.equal(math.calculateLineSpacing(Number.NaN, 70), 0);
});

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DroneEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Migrates the hardcoded camera_specs.js definitions into the new database tables.
     */
    public function run()
    {
        $now = Carbon::now();

        $cameras = [
            [
                'name' => 'DJI Zenmuse P1 (24mm)',
                'sensor_width_mm' => 35.9,
                'sensor_height_mm' => 24.0,
                'focal_length_mm' => 24.0,
                'image_width_px' => 8192,
                'image_height_px' => 5460,
                'min_photo_interval_sec' => 0.7, // DJI spec for P1
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'DJI Zenmuse P1 (35mm)',
                'sensor_width_mm' => 35.9,
                'sensor_height_mm' => 24.0,
                'focal_length_mm' => 35.0,
                'image_width_px' => 8192,
                'image_height_px' => 5460,
                'min_photo_interval_sec' => 0.7,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'DJI Mavic 3 Enterprise',
                'sensor_width_mm' => 17.3,
                'sensor_height_mm' => 13.0,
                'focal_length_mm' => 12.29,
                'image_width_px' => 5280,
                'image_height_px' => 3956,
                'min_photo_interval_sec' => 0.7,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'DJI Zenmuse H20/H20T (Wide)',
                'sensor_width_mm' => 6.17,
                'sensor_height_mm' => 4.55,
                'focal_length_mm' => 4.5,
                'image_width_px' => 4056,
                'image_height_px' => 3040,
                'min_photo_interval_sec' => null, // Unknown/Not typically used for mapping
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'DJI Zenmuse L1 (RGB)',
                'sensor_width_mm' => 13.2,
                'sensor_height_mm' => 8.8,
                'focal_length_mm' => 8.8,
                'image_width_px' => 5472,
                'image_height_px' => 3648,
                'min_photo_interval_sec' => null,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Do not insert duplicates
        foreach ($cameras as $cameraData) {
            DB::table('cameras')->updateOrInsert(
                ['name' => $cameraData['name']],
                $cameraData
            );
        }

        $drones = [
            [
                'name' => 'DJI Matrice 350 RTK',
                'usable_flight_time_min' => 40,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'DJI Mavic 3 Enterprise',
                'usable_flight_time_min' => 35,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'DJI Matrice 300 RTK',
                'usable_flight_time_min' => 35,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        foreach ($drones as $droneData) {
            DB::table('drones')->updateOrInsert(
                ['name' => $droneData['name']],
                $droneData
            );
        }
    }
}

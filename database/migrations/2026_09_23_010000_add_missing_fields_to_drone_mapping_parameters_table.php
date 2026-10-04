<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drone_mapping_parameters', function (Blueprint $table) {
            $table->string('drone_model')->nullable()->after('survey_location_id');
            $table->decimal('target_gsd_cm', 8, 2)->nullable()->after('altitude_m');
            $table->decimal('gsd_cm', 8, 2)->nullable()->after('target_gsd_cm');
            $table->decimal('ground_footprint_width_m', 8, 2)->nullable()->after('gsd_cm');
            $table->decimal('ground_footprint_height_m', 8, 2)->nullable()->after('ground_footprint_width_m');
            $table->decimal('photo_spacing_m', 8, 2)->nullable()->after('ground_footprint_height_m');
            $table->decimal('flight_line_spacing_m', 8, 2)->nullable()->after('photo_spacing_m');
            $table->decimal('photo_interval_s', 8, 2)->nullable()->after('flight_line_spacing_m');
            $table->decimal('usable_flight_time_min', 8, 2)->nullable()->after('photo_interval_s');
            $table->integer('sortie_count')->nullable()->after('usable_flight_time_min');
        });
    }

    public function down(): void
    {
        Schema::table('drone_mapping_parameters', function (Blueprint $table) {
            $table->dropColumn([
                'drone_model',
                'target_gsd_cm',
                'gsd_cm',
                'ground_footprint_width_m',
                'ground_footprint_height_m',
                'photo_spacing_m',
                'flight_line_spacing_m',
                'photo_interval_s',
                'usable_flight_time_min',
                'sortie_count'
            ]);
        });
    }
};

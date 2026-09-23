<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drone_mapping_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_location_id')->constrained()->onDelete('cascade');
            $table->string('camera_model')->nullable();
            $table->decimal('altitude_m', 8, 2)->default(100.00);
            $table->decimal('speed_ms', 5, 2)->default(10.00);
            $table->decimal('front_overlap_percent', 5, 2)->default(80.00);
            $table->decimal('side_overlap_percent', 5, 2)->default(70.00);
            $table->decimal('course_angle_deg', 5, 2)->default(0.00);
            $table->decimal('total_flight_distance_m', 10, 2)->nullable();
            $table->integer('total_images')->nullable();
            $table->decimal('estimated_duration_hours', 8, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drone_mapping_parameters');
    }
};

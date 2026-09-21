<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_boundaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('project_Id')->on('projects')->cascadeOnDelete();
            $table->foreignId('survey_location_id')->nullable()->constrained('survey_locations')->cascadeOnDelete();
            $table->json('geometry'); // GeoJSON
            $table->decimal('area', 15, 2)->default(0); // Square meters
            $table->decimal('perimeter', 15, 2)->default(0); // Meters
            $table->integer('vertex_count')->default(0);
            $table->json('centroid')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_boundaries');
    }
};

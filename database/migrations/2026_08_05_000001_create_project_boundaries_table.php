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
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
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

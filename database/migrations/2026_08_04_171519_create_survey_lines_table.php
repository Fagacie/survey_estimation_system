<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('survey_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('type')->default('main'); // main, cross, transit
            $table->integer('line_number')->nullable();
            $table->decimal('length_meters', 10, 2)->default(0);
            $table->decimal('bearing', 8, 2)->nullable();
            $table->json('geometry'); // Store GeoJSON geometry
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_lines');
    }
};

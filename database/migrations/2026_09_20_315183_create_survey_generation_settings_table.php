<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_generation_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('project_Id')->on('projects')->cascadeOnDelete();
            $table->foreignId('survey_location_id')->nullable()->constrained('survey_locations')->cascadeOnDelete();
            $table->decimal('line_spacing', 10, 2)->default(50);
            $table->decimal('orientation_angle', 10, 2)->default(0);
            $table->decimal('cross_line_spacing', 10, 2)->nullable();
            $table->decimal('cross_line_angle', 10, 2)->default(90);
            $table->decimal('margin', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_generation_settings');
    }
};

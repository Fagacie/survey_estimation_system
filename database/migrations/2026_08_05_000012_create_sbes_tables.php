<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sbes_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->double('survey_interval_m')->nullable();
            $table->double('survey_direction')->nullable();
            $table->double('survey_speed_knots')->nullable();
            $table->boolean('cross_line_enabled')->default(false);
            $table->integer('cross_line_count')->nullable();
            $table->double('working_hours_per_day')->default(8.0);
            $table->double('weather_days')->nullable();
            $table->double('mod_demod_days')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sbes_parameters');
    }
};

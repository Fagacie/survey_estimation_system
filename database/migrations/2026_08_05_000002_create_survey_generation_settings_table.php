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
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
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

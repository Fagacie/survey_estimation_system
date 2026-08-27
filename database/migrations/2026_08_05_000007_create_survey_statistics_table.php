<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->double('area')->nullable();
            $table->double('perimeter')->nullable();
            $table->double('total_distance')->nullable();
            $table->integer('line_count')->nullable();
            $table->double('average_length')->nullable();
            $table->double('longest_line')->nullable();
            $table->double('shortest_line')->nullable();
            $table->integer('vertex_count')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_statistics');
    }
};

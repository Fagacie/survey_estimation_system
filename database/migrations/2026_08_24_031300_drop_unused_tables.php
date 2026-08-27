<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('estimations');
        Schema::dropIfExists('survey_statistics');
        Schema::dropIfExists('settings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We do not provide a down method because recreating these tables
        // with their original structures would require duplicating the old blueprints.
        // This is a permanent cleanup migration.
    }
};

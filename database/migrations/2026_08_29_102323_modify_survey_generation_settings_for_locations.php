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
        Schema::table('survey_generation_settings', function (Blueprint $table) {
            $table->foreignId('survey_location_id')->nullable()->after('project_id')->constrained()->cascadeOnDelete();
        });
        
        // Try to backfill data from project_id where possible (if projects only have 1 survey location so far)
        \Illuminate\Support\Facades\DB::statement("
            UPDATE survey_generation_settings sgs
            JOIN survey_locations sl ON sl.project_id = sgs.project_id
            SET sgs.survey_location_id = sl.id
            WHERE sgs.survey_location_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_generation_settings', function (Blueprint $table) {
            $table->dropForeign(['survey_location_id']);
            $table->dropColumn('survey_location_id');
        });
    }
};

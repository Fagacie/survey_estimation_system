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
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('weather_days', 8, 2)->default(0)->after('description');
            $table->decimal('mod_demod_days', 8, 2)->default(0)->after('weather_days');
            $table->decimal('patch_test_days', 8, 2)->default(0)->after('mod_demod_days');
        });

        Schema::table('sbes_parameters', function (Blueprint $table) {
            $table->foreignId('survey_location_id')->nullable()->after('project_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('project_boundaries', function (Blueprint $table) {
            $table->foreignId('survey_location_id')->nullable()->after('project_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('survey_lines', function (Blueprint $table) {
            $table->foreignId('survey_location_id')->nullable()->after('project_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_lines', function (Blueprint $table) {
            $table->dropForeign(['survey_location_id']);
            $table->dropColumn('survey_location_id');
        });

        Schema::table('project_boundaries', function (Blueprint $table) {
            $table->dropForeign(['survey_location_id']);
            $table->dropColumn('survey_location_id');
        });

        Schema::table('sbes_parameters', function (Blueprint $table) {
            $table->dropForeign(['survey_location_id']);
            $table->dropColumn('survey_location_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['weather_days', 'mod_demod_days', 'patch_test_days']);
        });
    }
};

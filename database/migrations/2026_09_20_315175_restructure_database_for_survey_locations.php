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
        foreach (['weather_days', 'mod_demod_days', 'patch_test_days'] as $column) {
            if (!Schema::hasColumn('projects', $column)) {
                Schema::table('projects', function (Blueprint $table) use ($column) {
                    $table->decimal($column, 8, 2)->default(0);
                });
            }
        }

        foreach (['sbes_parameters', 'project_boundaries', 'survey_lines'] as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'survey_location_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('survey_location_id')->nullable()
                        ->constrained('survey_locations', 'id')->cascadeOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['weather_days', 'mod_demod_days', 'patch_test_days'] as $column) {
            if (Schema::hasColumn('projects', $column)) {
                Schema::table('projects', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        foreach (['sbes_parameters', 'project_boundaries', 'survey_lines'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'survey_location_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['survey_location_id']);
                    $table->dropColumn('survey_location_id');
                });
            }
        }
    }
};

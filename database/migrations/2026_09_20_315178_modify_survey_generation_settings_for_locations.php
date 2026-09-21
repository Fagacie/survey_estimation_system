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
        if (Schema::hasTable('survey_generation_settings') && !Schema::hasColumn('survey_generation_settings', 'survey_location_id')) {
            Schema::table('survey_generation_settings', function (Blueprint $table) {
                $table->foreignId('survey_location_id')->nullable()->after('project_id')
                    ->constrained('survey_locations', 'id')->cascadeOnDelete();
            });

            if (Schema::hasTable('survey_locations')) {
                \Illuminate\Support\Facades\DB::table('survey_generation_settings as sgs')
                    ->join('survey_locations as sl', 'sl.project_id', '=', 'sgs.project_id')
                    ->whereNull('sgs.survey_location_id')
                    ->update(['sgs.survey_location_id' => \Illuminate\Support\Facades\DB::raw('sl.id')]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('survey_generation_settings') && Schema::hasColumn('survey_generation_settings', 'survey_location_id')) {
            Schema::table('survey_generation_settings', function (Blueprint $table) {
                $table->dropForeign(['survey_location_id']);
                $table->dropColumn('survey_location_id');
            });
        }
    }
};

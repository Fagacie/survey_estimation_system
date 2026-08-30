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
        Schema::table('sbes_parameters', function (Blueprint $table) {
            if (!Schema::hasColumn('sbes_parameters', 'survey_speed_knots')) {
                $table->double('survey_speed_knots')->nullable()->after('total_distance_nm');
            }

            if (!Schema::hasColumn('sbes_parameters', 'working_hours_per_day')) {
                $table->double('working_hours_per_day')->nullable()->after('survey_speed_knots');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sbes_parameters', function (Blueprint $table) {
            if (Schema::hasColumn('sbes_parameters', 'survey_speed_knots')) {
                $table->dropColumn('survey_speed_knots');
            }

            if (Schema::hasColumn('sbes_parameters', 'working_hours_per_day')) {
                $table->dropColumn('working_hours_per_day');
            }
        });
    }
};

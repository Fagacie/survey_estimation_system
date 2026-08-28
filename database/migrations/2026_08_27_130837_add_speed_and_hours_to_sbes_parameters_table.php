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
            $table->double('survey_speed_knots')->nullable()->after('total_distance_nm');
            $table->double('working_hours_per_day')->nullable()->after('survey_speed_knots');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sbes_parameters', function (Blueprint $table) {
            $table->dropColumn(['survey_speed_knots', 'working_hours_per_day']);
        });
    }
};

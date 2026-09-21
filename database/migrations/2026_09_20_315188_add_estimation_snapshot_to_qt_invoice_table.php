<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qt_invoice', function (Blueprint $table) {
            $table->decimal('survey_distance_nm', 12, 4)->nullable()->after('grand_total');
            $table->decimal('survey_hours', 12, 2)->nullable()->after('survey_distance_nm');
            $table->decimal('survey_duration_days', 12, 2)->nullable()->after('survey_hours');
        });
    }

    public function down(): void
    {
        Schema::table('qt_invoice', function (Blueprint $table) {
            $table->dropColumn(['survey_distance_nm', 'survey_hours', 'survey_duration_days']);
        });
    }
};

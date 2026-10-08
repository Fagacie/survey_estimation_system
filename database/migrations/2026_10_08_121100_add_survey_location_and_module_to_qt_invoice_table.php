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
        Schema::table('qt_invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('survey_location_id')->nullable()->after('project_Id');
            $table->string('module_type', 50)->nullable()->after('survey_location_id'); // 'sbes', 'drone', 'modelling'
            $table->json('snapshot_data')->nullable()->after('module_type'); // Store frozen metrics at time of quotation
            
            // Add foreign key constraint
            $table->foreign('survey_location_id', 'fk_qt_invoice_survey_location')
                  ->references('id')
                  ->on('survey_locations')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qt_invoice', function (Blueprint $table) {
            $table->dropForeign('fk_qt_invoice_survey_location');
            $table->dropColumn(['survey_location_id', 'module_type', 'snapshot_data']);
        });
    }
};

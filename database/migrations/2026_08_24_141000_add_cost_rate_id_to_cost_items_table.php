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
        Schema::table('cost_items', function (Blueprint $table) {
            $table->foreignId('cost_rate_id')->nullable()->after('cost_estimation_id')->constrained('cost_rates')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_items', function (Blueprint $table) {
            $table->dropForeign(['cost_rate_id']);
            $table->dropColumn('cost_rate_id');
        });
    }
};

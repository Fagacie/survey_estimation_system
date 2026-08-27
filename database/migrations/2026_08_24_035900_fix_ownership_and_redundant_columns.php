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
            // nullable for now to avoid breaking existing records, we can attach them to user 1 later if needed.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('id');
        });

        Schema::table('cost_items', function (Blueprint $table) {
            $table->dropColumn('module_type');
        });

        Schema::table('cost_rates', function (Blueprint $table) {
            $table->dropColumn('survey_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('cost_items', function (Blueprint $table) {
            $table->string('module_type')->default('GENERAL');
        });

        Schema::table('cost_rates', function (Blueprint $table) {
            $table->string('survey_type')->default('SBES');
        });
    }
};

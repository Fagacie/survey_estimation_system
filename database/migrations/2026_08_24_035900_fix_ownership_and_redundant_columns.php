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
        if (!Schema::hasColumn('projects', 'user_id')) {
            Schema::table('projects', function (Blueprint $table) {
                // nullable for now to avoid breaking existing records, we can attach them to user 1 later if needed.
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('id');
            });
        }

        if (Schema::hasColumn('cost_items', 'module_type')) {
            Schema::table('cost_items', function (Blueprint $table) {
                $table->dropColumn('module_type');
            });
        }

        if (Schema::hasColumn('cost_rates', 'survey_type')) {
            Schema::table('cost_rates', function (Blueprint $table) {
                $table->dropColumn('survey_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('projects', 'user_id')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            });
        }

        if (!Schema::hasColumn('cost_items', 'module_type')) {
            Schema::table('cost_items', function (Blueprint $table) {
                $table->string('module_type')->default('GENERAL');
            });
        }

        if (!Schema::hasColumn('cost_rates', 'survey_type')) {
            Schema::table('cost_rates', function (Blueprint $table) {
                $table->string('survey_type')->default('SBES');
            });
        }
    }
};

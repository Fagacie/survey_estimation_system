<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cost_estimations', function (Blueprint $table) {
            $table->string('quotation_number')->nullable()->unique()->after('status');
            $table->text('terms_conditions')->nullable()->after('quotation_number');
            $table->date('valid_until')->nullable()->after('terms_conditions');
            $table->json('duration_breakdown')->nullable()->after('valid_until');
        });

        if (Schema::hasTable('reports')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->string('type')->default('full_report')->after('report_number');
            });
        }
    }

    public function down(): void
    {
        Schema::table('cost_estimations', function (Blueprint $table) {
            $table->dropColumn(['quotation_number', 'terms_conditions', 'valid_until', 'duration_breakdown']);
        });

        if (Schema::hasTable('reports') && Schema::hasColumn('reports', 'type')) {
            Schema::table('reports', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};

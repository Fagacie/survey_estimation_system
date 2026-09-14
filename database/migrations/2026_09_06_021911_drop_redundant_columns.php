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
            if (Schema::hasColumn('projects', 'client')) {
                $table->dropColumn('client');
            }
        });

        Schema::table('sbes_parameters', function (Blueprint $table) {
            if (Schema::hasColumn('sbes_parameters', 'weather_days')) {
                $table->dropColumn('weather_days');
            }
            if (Schema::hasColumn('sbes_parameters', 'mod_demod_days')) {
                $table->dropColumn('mod_demod_days');
            }
            if (Schema::hasColumn('sbes_parameters', 'patch_test_days')) {
                $table->dropColumn('patch_test_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('client')->nullable();
        });

        Schema::table('sbes_parameters', function (Blueprint $table) {
            $table->decimal('weather_days', 8, 2)->default(0);
            $table->decimal('mod_demod_days', 8, 2)->default(0);
            $table->decimal('patch_test_days', 8, 2)->default(0);
        });
    }
};

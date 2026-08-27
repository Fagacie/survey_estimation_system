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
            $table->decimal('patch_test_days', 8, 2)->default(0)->after('mod_demod_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('sbes_parameters', function (Blueprint $table) {
            $table->dropColumn(['patch_test_days']);
        });
    }
};

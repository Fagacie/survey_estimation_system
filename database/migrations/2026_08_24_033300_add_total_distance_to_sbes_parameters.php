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
            $table->double('total_distance_nm')->default(0)->after('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sbes_parameters', function (Blueprint $table) {
            $table->dropColumn('total_distance_nm');
        });
    }
};

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
        Schema::table('drone_mapping_parameters', function (Blueprint $table) {
            $table->decimal('mapping_margin_m', 8, 2)->nullable()->after('sortie_count');
            $table->string('capture_mode')->nullable()->after('mapping_margin_m');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drone_mapping_parameters', function (Blueprint $table) {
            $table->dropColumn(['mapping_margin_m', 'capture_mode']);
        });
    }
};

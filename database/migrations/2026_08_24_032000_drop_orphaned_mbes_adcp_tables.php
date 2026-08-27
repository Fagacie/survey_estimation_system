<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('mbes_parameters');
        Schema::dropIfExists('mbes_borders');
        Schema::dropIfExists('adcp_parameters');
        Schema::dropIfExists('sbes_rivers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We do not provide a down method because recreating these tables
        // with their original structures would require duplicating the old blueprints.
        // This is a permanent cleanup migration.
    }
};

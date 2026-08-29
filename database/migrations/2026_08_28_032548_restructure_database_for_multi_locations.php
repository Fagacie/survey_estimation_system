<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * NOTE: This migration is intentionally empty. The multi-location database
     * restructure is planned but not yet implemented. Once requirements are finalized,
     * add the schema changes here (e.g., projects_locations table, foreign keys, etc.)
     * to support multi-location survey projects.
     */
    public function up(): void
    {
        // TODO: Implement multi-location schema when feature is ready
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No schema to revert yet
    }
};

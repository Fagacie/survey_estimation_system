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
            $table->renameColumn('quantity', 'days');
            $table->renameColumn('units', 'qty');
        });

        Schema::table('cost_items', function (Blueprint $table) {
            $table->integer('persons')->nullable()->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_items', function (Blueprint $table) {
            $table->dropColumn('persons');
        });
        
        Schema::table('cost_items', function (Blueprint $table) {
            $table->renameColumn('days', 'quantity');
            $table->renameColumn('qty', 'units');
        });
    }
};

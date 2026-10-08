<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_default_items', function (Blueprint $table) {
            $table->id();
            $table->string('survey_type', 50);               // single_beam, drone_mapping
            $table->unsignedBigInteger('item_id');            // points to items.item_id
            $table->unsignedInteger('default_qty')->default(1);
            $table->string('days_rule', 20)->default('fixed'); // fixed, execution, total
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['survey_type', 'item_id']);
            $table->foreign('item_id')->references('item_id')->on('items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_default_items');
    }
};
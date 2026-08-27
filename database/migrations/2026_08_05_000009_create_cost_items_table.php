<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_estimation_id')->constrained()->cascadeOnDelete();
            $table->string('module_type')->default('GENERAL'); // MBES, SBES, ADCP, GENERAL
            $table->string('category');
            $table->string('description');
            $table->double('quantity')->default(1);
            $table->double('unit_rate')->default(0);
            $table->double('total_price')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_items');
    }
};

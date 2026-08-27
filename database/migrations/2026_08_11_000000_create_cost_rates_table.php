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
        Schema::create('cost_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit_type')->default('Per Day'); // 'Per Day', 'Lump Sum'
            $table->decimal('default_rate', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_rates');
    }
};

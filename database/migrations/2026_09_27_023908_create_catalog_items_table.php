<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('catalog_modules')->cascadeOnDelete();
            $table->string('work_package'); // e.g. Model Setup, Simulation, Software
            $table->string('name');
            $table->decimal('qty', 8, 2)->default(1);
            $table->decimal('rate', 12, 2);   // MYR per day
            $table->decimal('days', 6, 2)->default(1);
            $table->decimal('markup', 5, 2)->default(30); // percent
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('catalog_items');
    }
};
 

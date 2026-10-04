<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_package', function (Blueprint $table) {
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained('catalog_modules')->cascadeOnDelete();
            $table->primary(['package_id', 'module_id']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('module_package');
    }
};
 

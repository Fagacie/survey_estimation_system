<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_modelling_items', function (Blueprint $table) {
            $table->id();
 
            // projects.project_Id is a custom-named primary key (not the default "id"),
            // so we spell out the foreign key by hand instead of foreignId()->constrained().
            $table->unsignedBigInteger('project_Id');
            $table->foreign('project_Id')->references('project_Id')->on('projects')->cascadeOnDelete();
 
            // catalog_modules / catalog_items use the default "id" PK, so the shorthand works here.
            $table->foreignId('catalog_module_id')->constrained('catalog_modules')->cascadeOnDelete();
            $table->foreignId('catalog_item_id')->constrained('catalog_items')->cascadeOnDelete();
 
            // Same column names as QtInvoiceItem (unit_qty, days, daily_rate, mark_up, line_total)
            // so the two look and feel the same wherever they're used together.
            $table->decimal('unit_qty', 8, 2)->default(1);
            $table->decimal('days', 6, 2)->default(1);
            $table->decimal('daily_rate', 12, 2);
            $table->decimal('mark_up', 5, 2)->default(30);
            $table->decimal('line_total', 12, 2);
 
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('project_modelling_items');
    }
};
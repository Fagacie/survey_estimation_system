<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_modelling_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_Id')->unique();
            $table->foreign('project_Id')->references('project_Id')->on('projects')->cascadeOnDelete();

            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->string('package_name')->nullable(); // snapshot, survives package rename/delete

            $table->decimal('contingency_percent', 5, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);

            $table->decimal('internal_subtotal', 14, 2)->default(0);
            $table->decimal('client_subtotal', 14, 2)->default(0);
            $table->decimal('contingency_amount', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_modelling_summaries');
    }
};
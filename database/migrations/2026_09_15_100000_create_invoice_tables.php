<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── INVOICES TABLE ──────────────────────────────────────────
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();

            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('client_id')->constrained('clients')->restrictOnDelete();
            $table->foreignId('cost_estimation_id')->nullable()->constrained('cost_estimations')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->enum('status', ['Draft', 'Issued', 'Paid', 'Overdue', 'Cancelled'])->default('Draft');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('payment_terms', 100)->nullable();

            $table->string('client_ref', 100)->nullable();
            $table->string('our_ref', 100)->nullable();
            $table->string('currency', 10)->default('MYR');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->nullable();
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('amount_in_words', 500)->nullable();

            $table->string('payment_bank_name', 255)->nullable();
            $table->string('payment_account_name', 255)->nullable();
            $table->string('payment_account_number', 50)->nullable();
            $table->string('payment_swift_code', 20)->nullable();

            $table->text('notes')->nullable();

            $table->string('prepared_by_name', 100)->nullable();
            $table->string('prepared_by_title', 100)->nullable();
            $table->string('approved_by_name', 100)->nullable();
            $table->string('approved_by_title', 100)->nullable();

            $table->timestamps();

            $table->index('status');
        });

        // ── INVOICE ITEMS TABLE ─────────────────────────────────────
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->timestamps();
        });

        // ── COMPANY SETTINGS TABLE ──────────────────────────────────
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('company_settings');
    }
};

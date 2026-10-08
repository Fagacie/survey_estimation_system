<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qt_invoice_items', function (Blueprint $table) {
            $table->string('custom_item_name', 255)->nullable()->after('catalog_item_id');
            $table->unsignedBigInteger('category_id')->nullable()->after('custom_item_name');
        });
    }

    public function down(): void
    {
        Schema::table('qt_invoice_items', function (Blueprint $table) {
            $table->dropColumn(['custom_item_name', 'category_id']);
        });
    }
};
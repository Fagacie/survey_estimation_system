<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Which person signs this quotation ("Yours sincerely"). Uses the same signatories table as invoices.
        Schema::table('qt_invoice', function (Blueprint $table) {
            $table->unsignedBigInteger('signatory_id')->nullable()->after('additional_notes');
        });
    }

    public function down(): void
    {
        Schema::table('qt_invoice', function (Blueprint $table) {
            $table->dropColumn('signatory_id');
        });
    }
};
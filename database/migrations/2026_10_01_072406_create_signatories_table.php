<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. People who can sign ("Approved by") on an invoice
        Schema::create('signatories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('signature_path')->nullable();   // e.g. images/signatures/approved_by.png
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 2. Which signer was chosen for each invoice
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('signatory_id')->nullable()->after('description');
        });

        // 3. The signer that was fixed on the slip until now, so he/she appears in the dropdown
        //    and old invoices (signatory_id = null) keep showing the same person.
        DB::table('signatories')->insert([
            'name'           => 'Ts. Dr. Mardiha Mokhtar',
            'position'       => 'Technical Director',
            'signature_path' => 'images/signatures/approved_by.png',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('signatory_id');
        });

        Schema::dropIfExists('signatories');
    }
};
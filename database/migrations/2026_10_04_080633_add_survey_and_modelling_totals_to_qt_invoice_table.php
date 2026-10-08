<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('qt_invoice', function (Blueprint $table) {
            $table->decimal('survey_total', 12, 2)->default(0)->after('grand_total');
            $table->decimal('modelling_total', 12, 2)->default(0)->after('survey_total');
        });

        // Old quotations have no modelling, so survey total = their current grand_total
        DB::table('qt_invoice')->update(['survey_total' => DB::raw('grand_total')]);
    }

    public function down()
    {
        Schema::table('qt_invoice', function (Blueprint $table) {
            $table->dropColumn(['survey_total', 'modelling_total']);
        });
    }
};
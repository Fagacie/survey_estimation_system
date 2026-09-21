<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('survey_lines')) {
            return;
        }

        DB::table('survey_lines')
            ->whereNotIn('type', ['main', 'cross', 'reference', 'adcp_marker'])
            ->update(['type' => 'main']);
    }

    public function down(): void
    {
        //
    }
};

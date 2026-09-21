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
            ->select('id', 'geometry')
            ->orderBy('id')
            ->chunkById(200, function ($lines) {
                foreach ($lines as $line) {
                    $geometry = is_string($line->geometry)
                        ? json_decode($line->geometry, true)
                        : $line->geometry;

                    $lineType = $geometry['properties']['line_type'] ?? null;

                    if (in_array($lineType, ['main', 'cross', 'reference', 'adcp_marker'], true)) {
                        DB::table('survey_lines')
                            ->where('id', $line->id)
                            ->update(['type' => $lineType]);
                    }
                }
            });
    }

    public function down(): void
    {
        //
    }
};

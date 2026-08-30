<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$lines = DB::table("survey_lines")->where("survey_location_id", 1)->get()->map(function($l) {
    return is_string($l->geometry) ? json_decode($l->geometry, true) : $l->geometry;
})->filter();

echo count($lines);

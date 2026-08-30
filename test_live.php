<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$c = $app->make(App\Http\Controllers\SurveyLocationController::class);
$s = App\Models\SurveyLocation::find(2);
$p = $s->project;

try {
    $lines = $c->mapLines($p, $s);
    echo substr(json_encode($lines->getData()), 0, 800) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

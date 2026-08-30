<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$c = $app->make(App\Http\Controllers\SurveyLocationController::class);
$p = App\Models\Project::find(1);
$s = App\Models\SurveyLocation::find(2);

try {
    $lines = $c->mapLines($p, $s);
    echo "Number of lines: " . count($lines) . "\n";
    echo "First line: " . substr(json_encode($lines[0]), 0, 300) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

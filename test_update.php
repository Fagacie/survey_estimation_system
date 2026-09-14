<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$project = \App\Models\Project::find(7);
if ($project) {
    echo $project->surveyLocations->toJson(JSON_PRETTY_PRINT);
}

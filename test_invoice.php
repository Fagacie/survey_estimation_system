<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$project = \App\Models\Project::has('surveyLocations')->first();
if ($project) {
    echo "Original survey locations count: " . count($project->surveyLocations) . "\n";
} else {
    echo "No projects found.\n";
}

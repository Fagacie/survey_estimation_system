<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first() ?? \App\Models\User::factory()->create();
$project = \App\Models\Project::create([
    'name' => 'Test', 'client_Id' => 1, 'number' => '123', 'period' => '1', 'pic_name' => '1', 'pic_no' => '1', 'created_by' => $user->id
]);
$loc1 = $project->surveyLocations()->create(['name' => 'Hydro', 'survey_type' => 'single_beam', 'status' => 'completed']);
$loc2 = $project->surveyLocations()->create(['name' => 'Drone', 'survey_type' => 'drone', 'status' => 'completed']);

$prefillProject = \App\Models\Project::with(['client'])->whereKey($project->project_Id)->firstOrFail();
$location_id = (string) $loc2->id;

$prefillProject->unsetRelation('surveyLocations');
$prefillProject->load('surveyLocations');
$c1 = count($prefillProject->surveyLocations);

$filtered = $prefillProject->surveyLocations->where('id', $location_id)->values();
$prefillProject->setRelation('surveyLocations', $filtered);
$c2 = count($prefillProject->surveyLocations);

$prefillProject->loadMissing(['surveyLocations.sbesParameters', 'surveyLocations.droneMappingParameters']);
$c3 = count($prefillProject->surveyLocations);

file_put_contents('test_out.txt', "Before: $c1 | After set: $c2 | After loadMissing: $c3");

$loc1->delete();
$loc2->delete();
$project->delete();

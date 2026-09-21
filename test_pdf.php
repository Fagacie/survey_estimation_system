<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::has('projects')->first();
Auth::login($user);
$project = $user->projects()->first();

try {
    $response = app(\App\Http\Controllers\ReportController::class)->download($project->project_Id);
    echo "SUCCESS: " . get_class($response) . "\n";
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}

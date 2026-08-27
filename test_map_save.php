<?php require __DIR__."/vendor/autoload.php"; $app = require_once __DIR__."/bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$project = App\Models\Project::first();
$mapService = app(App\Services\Map\MapService::class);
try {
    $stats = $mapService->processMapData($project, ["boundaries" => []]);
    echo "SUCCESS\n";
} catch (Throwable $e) {
    echo $e;
}


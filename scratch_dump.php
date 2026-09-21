<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$params = \App\Models\SbesParameter::all();
echo json_encode($params->toArray(), JSON_PRETTY_PRINT);

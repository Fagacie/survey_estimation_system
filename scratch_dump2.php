<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$params = \App\Models\SbesParameter::all();
file_put_contents(__DIR__.'/scratch_output.txt', json_encode($params->toArray(), JSON_PRETTY_PRINT));

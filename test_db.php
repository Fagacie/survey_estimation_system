<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$start = microtime(true);
App\Models\Project::create(['project_code'=>'TEST-998','name'=>'Test','status'=>'draft']);
echo 'Took: ' . (microtime(true) - $start) . 's\n';

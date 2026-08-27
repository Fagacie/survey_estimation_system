<?php
require 'vendor/autoload.php';
$start = microtime(true);
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/projects/24', 'GET');
echo "Make Req: " . (microtime(true) - $start) . "s\n";
$start = microtime(true);
$response = $kernel->handle($request);
echo "Handle: " . (microtime(true) - $start) . "s\n";
echo "Status: " . $response->getStatusCode() . "\n";

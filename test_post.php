<?php
require 'vendor/autoload.php';
$start = microtime(true);
$app = require_once 'bootstrap/app.php';
echo "Boot App: " . (microtime(true) - $start) . "s\n";
$start = microtime(true);
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
echo "Make Kernel: " . (microtime(true) - $start) . "s\n";
$start = microtime(true);
$request = Illuminate\Http\Request::create('/projects', 'POST', ['name'=>'T', 'status'=>'draft']);
echo "Make Req: " . (microtime(true) - $start) . "s\n";
$start = microtime(true);
$kernel->handle($request);
echo "Handle: " . (microtime(true) - $start) . "s\n";

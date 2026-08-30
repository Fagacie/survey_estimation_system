<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth()->login($user);

$req = Illuminate\Http\Request::create("/projects/1/surveys/1/map-lines", "GET");
$response = app()->handle($req);
echo $response->getContent();

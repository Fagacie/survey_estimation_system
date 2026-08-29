<?php
// Create test user for testing
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Create user
$user = \App\Models\User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => bcrypt('Password123!')
]);

echo "✅ Test user created successfully!\n";
echo "Email: test@example.com\n";
echo "Password: Password123!\n";
?>

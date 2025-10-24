<?php
// test_admin_route_management.php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\RouteManagement\RouteController;
use App\Models\User;

// Ensure driver/admin user exists
$user = User::where('email', 'admin@zar.com')->first();
if (!$user) {
    $user = User::create([
        'name' => 'System Admin',
        'email' => 'admin@zar.com',
        'password' => bcrypt('Admin@12345'),
        'role' => 'Admin'
    ]);
}

// Instantiate controller and call index()
$controller = new RouteController();
$response = $controller->index();

if ($response instanceof Illuminate\Contracts\View\View) {
    echo $response->render();
} elseif ($response instanceof Illuminate\Http\Response) {
    echo $response->getContent();
} else {
    var_dump($response);
}

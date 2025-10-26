<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('role', 'Technician')->first();
echo "User: " . $user->name . " Role: " . $user->role . "\n";
echo "Role check: " . (in_array($user->role, ['Admin', 'Technician']) ? 'true' : 'false') . "\n";

$user2 = User::where('role', 'Driver')->first();
echo "User: " . $user2->name . " Role: " . $user2->role . "\n";
echo "Role check: " . (in_array($user2->role, ['Admin', 'Technician']) ? 'true' : 'false') . "\n";

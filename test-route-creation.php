<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\RouteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

try {
    echo "Testing Route Creation via Controller...\n";
    
    // Simulate user authentication (assuming user ID 1 exists)
    $user = App\Models\User::find(1);
    if (!$user) {
        echo "✗ No user found with ID 1. Creating a test user...\n";
        $user = App\Models\User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'Admin'
        ]);
        echo "✓ Test user created with ID: {$user->id}\n";
    }
    
    Auth::login($user);
    echo "✓ User authenticated: {$user->name} (ID: {$user->id})\n";
    
    // Create test request data
    $requestData = [
        'route_name' => 'Test Route via Controller',
        'route_code' => 'CTRL-' . time(),
        'description' => 'Test route created via controller',
        'start_location' => 'Test Start Location',
        'end_location' => 'Test End Location',
        'route_type' => 'delivery',
        'priority' => 'medium',
        'total_distance' => 15.5,
        'estimated_duration' => 90,
        'start_time' => '08:00',
        'end_time' => '17:00',
        'fuel_cost_estimate' => 25.50,
        'special_instructions' => 'Test instructions',
        'checkpoints' => [
            [
                'name' => 'Checkpoint 1',
                'address' => '123 Test Street',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'type' => 'pickup',
                'duration' => 15,
                'instructions' => 'First checkpoint',
                'contact_info' => 'test@example.com',
                'mandatory' => true
            ],
            [
                'name' => 'Checkpoint 2',
                'address' => '456 Test Avenue',
                'latitude' => 40.7589,
                'longitude' => -73.9851,
                'type' => 'delivery',
                'duration' => 20,
                'instructions' => 'Second checkpoint',
                'contact_info' => 'test2@example.com',
                'mandatory' => true
            ]
        ]
    ];
    
    // Create request object
    $request = new Request($requestData);
    $request->setMethod('POST');
    
    // Create controller instance
    $controller = new RouteController();
    
    echo "✓ Attempting to create route via controller...\n";
    
    // Call the store method
    $response = $controller->store($request);
    
    echo "✓ Controller method executed successfully\n";
    echo "Response type: " . get_class($response) . "\n";
    
    // Check if route was created
    $latestRoute = App\Models\Route::latest()->first();
    if ($latestRoute && $latestRoute->route_code === $requestData['route_code']) {
        echo "✓ Route created successfully!\n";
        echo "Route ID: {$latestRoute->id}\n";
        echo "Route Name: {$latestRoute->route_name}\n";
        echo "Route Code: {$latestRoute->route_code}\n";
        echo "Checkpoints: " . $latestRoute->checkpoints()->count() . "\n";
        
        // Clean up
        $latestRoute->delete();
        echo "✓ Test route cleaned up\n";
    } else {
        echo "✗ Route was not created or not found\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
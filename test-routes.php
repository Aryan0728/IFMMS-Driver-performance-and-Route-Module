<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing Route functionality...\n";
    
    // Check if routes table exists and count
    $routeCount = App\Models\Route::count();
    echo "Current routes in database: $routeCount\n";
    
    // Show existing routes
    if ($routeCount > 0) {
        echo "\nExisting routes:\n";
        App\Models\Route::take(5)->get()->each(function($route) {
            echo "- {$route->route_name} ({$route->route_code}) - Status: {$route->status}\n";
        });
    }
    
    // Test creating a simple route
    echo "\nTesting route creation...\n";
    
    $testRoute = App\Models\Route::create([
        'route_name' => 'Test Route ' . time(),
        'route_code' => 'TEST-' . time(),
        'description' => 'Test route for debugging',
        'start_location' => 'Test Start Location',
        'end_location' => 'Test End Location',
        'route_type' => 'delivery',
        'priority' => 'medium',
        'status' => 'active',
        'total_distance' => 10.5,
        'estimated_duration' => 60,
        'created_by' => 1 // Assuming user ID 1 exists
    ]);
    
    echo "✓ Route created successfully with ID: {$testRoute->id}\n";
    echo "Route details: {$testRoute->route_name} - {$testRoute->route_code}\n";
    
    // Clean up test route
    $testRoute->delete();
    echo "✓ Test route cleaned up\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
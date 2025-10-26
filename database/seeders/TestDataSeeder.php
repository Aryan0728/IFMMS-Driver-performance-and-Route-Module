<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\RouteAssignment;
use App\Models\RouteCheckpoint;
use App\Models\DriverMetric;
use App\Models\DriverPerformance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create 10 drivers
        $drivers = [];
        for ($i = 1; $i <= 10; $i++) {
            $driver = User::firstOrCreate(
                ['email' => "driver{$i}@example.com"],
                [
                    'name' => "Driver {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'Driver',
                ]
            );
            $drivers[] = $driver;
        }

        // Create 10 vehicles
        $vehicles = [];
        $makes = ['Toyota', 'Ford', 'Honda', 'Chevrolet', 'Nissan', 'BMW', 'Mercedes', 'Volvo', 'Audi', 'Tesla'];
        $models = ['F-150', 'Civic', 'Silverado', 'Altima', 'X5', 'C-Class', 'XC90', 'A4', 'Model 3', 'Explorer'];

        for ($i = 1; $i <= 10; $i++) {
            $vehicle = Vehicle::firstOrCreate(
                ['license_plate' => "VEH{$i}00"],
                [
                    'vehicle_number' => "V{$i}00",
                    'make' => $makes[$i-1],
                    'model' => $models[$i-1],
                    'year' => 2020 + rand(0, 4),
                    'vin' => "VIN" . str_pad($i, 10, '0', STR_PAD_LEFT),
                    'driver_id' => $drivers[$i-1]->id,
                    'status' => 'active',
                    'mileage' => rand(10000, 100000),
                    'fuel_type' => ['gasoline', 'diesel', 'electric'][rand(0, 2)],
                    'health_score' => rand(70, 100),
                ]
            );
            $vehicles[] = $vehicle;
        }

        // Create 10 routes with waypoints
        $routes = [];
        $routeNames = [
            'Downtown Delivery Route',
            'Suburban Service Route',
            'Industrial Zone Route',
            'Airport Cargo Route',
            'City Center Route',
            'Highway Express Route',
            'Residential Delivery Route',
            'Commercial District Route',
            'Warehouse Distribution Route',
            'Emergency Response Route'
        ];

        for ($i = 1; $i <= 10; $i++) {
            $waypoints = [];
            $numWaypoints = rand(3, 8);

            for ($j = 0; $j < $numWaypoints; $j++) {
                $waypoints[] = [
                    'name' => "Waypoint " . ($j + 1),
                    'lat' => 40.7128 + (rand(-500, 500) / 10000),
                    'lng' => -74.0060 + (rand(-500, 500) / 10000)
                ];
            }

            $route = Route::firstOrCreate(
                ['route_code' => 'RT' . str_pad($i, 3, '0', STR_PAD_LEFT)],
                [
                    'name' => $routeNames[$i-1],
                    'assigned_driver_id' => $drivers[$i-1]->id,
                    'vehicle_id' => $vehicles[$i-1]->id,
                    'start_time' => now()->addDays(rand(0, 7))->setTime(8, 0),
                    'estimated_end_time' => now()->addDays(rand(0, 7))->setTime(17, 0),
                    'status' => 'planned',
                    'optimized_waypoints' => $waypoints,
                    'estimated_distance' => rand(20, 200),
                    'estimated_duration' => rand(60, 480),
                    'notes' => "Special instructions for {$routeNames[$i-1]}",
                ]
            );
            $routes[] = $route;
        }

        // Create driver performance data for the last 30 days
        foreach ($drivers as $index => $driver) {
            for ($days = 0; $days < 30; $days++) {
                $date = Carbon::now()->subDays($days);

                DriverPerformance::create([
                    'driver_id' => $driver->id,
                    'period_start' => $date->startOfDay(),
                    'period_end' => $date->endOfDay(),
                    'period_type' => 'daily',
                    'total_distance' => rand(50, 300),
                    'total_routes' => rand(1, 5),
                    'average_fuel_efficiency' => rand(15, 35),
                    'average_speed' => rand(25, 65),
                    'on_time_percentage' => rand(70, 100),
                    'safety_score' => rand(70, 100),
                    'customer_rating' => rand(30, 50) / 10,
                    'performance_score' => rand(70, 100),
                ]);
            }
        }

        // Create driver metrics for the last 30 days
        foreach ($drivers as $index => $driver) {
            for ($days = 0; $days < 30; $days++) {
                $date = Carbon::now()->subDays($days);

                DriverMetric::create([
                    'driver_id' => $driver->id,
                    'record_date' => $date,
                    'miles_driven' => rand(50, 300),
                    'fuel_consumed' => rand(5, 25),
                    'deliveries_completed' => rand(5, 20),
                    'on_time_percentage' => rand(70, 100),
                    'hard_brakes' => rand(0, 10),
                    'rapid_accelerations' => rand(0, 8),
                    'speeding_incidents' => rand(0, 3),
                    'score' => rand(70, 100),
                ]);
            }
        }

        echo "Test data seeded successfully!\n";
        echo "Created:\n";
        echo "- 10 Drivers\n";
        echo "- 10 Vehicles\n";
        echo "- 10 Routes with waypoints\n";
        echo "- 300 Driver Performance records (30 days × 10 drivers)\n";
        echo "- 300 Driver Metric records (30 days × 10 drivers)\n";
    }
}

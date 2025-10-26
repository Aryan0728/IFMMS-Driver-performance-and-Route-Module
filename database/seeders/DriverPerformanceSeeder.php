<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\DriverMetric;
use App\Models\Route;
use App\Models\RouteLog;
use Carbon\Carbon;

class DriverPerformanceSeeder extends Seeder
{
    public function run()
    {
        // Create sample drivers if they don't exist
        $driver1 = User::firstOrCreate(
            ['email' => 'driver1@example.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('password'),
                'role' => 'Driver',
            ]
        );

        $driver2 = User::firstOrCreate(
            ['email' => 'driver2@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => bcrypt('password'),
                'role' => 'Driver',
            ]
        );

        // Create sample vehicles
        $vehicle1 = Vehicle::firstOrCreate(
            ['license_plate' => 'ABC123'],
            [
                'vehicle_number' => 'V001',
                'vin' => 'VIN123456789',
                'make' => 'Ford',
                'model' => 'F-150',
                'year' => 2022,
                'driver_id' => $driver1->id,
                'status' => 'active',
                'mileage' => 15000,
            ]
        );

        $vehicle2 = Vehicle::firstOrCreate(
            ['license_plate' => 'XYZ789'],
            [
                'vehicle_number' => 'V002',
                'vin' => 'VIN987654321',
                'make' => 'Chevrolet',
                'model' => 'Silverado',
                'year' => 2023,
                'driver_id' => $driver2->id,
                'status' => 'active',
                'mileage' => 8000,
            ]
        );

        // Create a sample route if it doesn't exist
        $route = Route::firstOrCreate(
            ['route_code' => 'SAMPLE001'],
            [
                'route_name' => 'Sample Route 1',
                'start_location' => 'New York, NY',
                'end_location' => 'Boston, MA',
                'total_distance' => 200,
                'estimated_duration' => 240,
                'created_by' => 1, // Assume admin ID 1
            ]
        );

        // Create sample RouteLog entries for analysis
        RouteLog::create([
            'route_id' => $route->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'odometer' => 100,
            'speed' => 0,
            'fuel_level' => 10,
            'location_name' => 'Start',
            'recorded_at' => Carbon::yesterday()->startOfDay()->addHours(8),
        ]);

        RouteLog::create([
            'route_id' => $route->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'odometer' => 110,
            'speed' => 60,
            'fuel_level' => 9.5,
            'location_name' => 'Highway',
            'recorded_at' => Carbon::yesterday()->startOfDay()->addHours(9),
        ]);

        RouteLog::create([
            'route_id' => $route->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'odometer' => 120,
            'speed' => 70,
            'fuel_level' => 9,
            'location_name' => 'Highway',
            'recorded_at' => Carbon::yesterday()->startOfDay()->addHours(10),
        ]);

        RouteLog::create([
            'route_id' => $route->id,
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'odometer' => 130,
            'speed' => 0,
            'fuel_level' => 8.5,
            'location_name' => 'Stop',
            'recorded_at' => Carbon::yesterday()->startOfDay()->addHours(11),
        ]);

        // Create sample DriverMetrics directly for quick testing
        DriverMetric::create([
            'driver_id' => $driver1->id,
            'vehicle_id' => $vehicle1->id,
            'record_date' => Carbon::yesterday(),
            'miles_driven' => 20,
            'fuel_consumed' => 2.5,
            'deliveries_completed' => 5,
            'on_time_percentage' => 95,
            'hard_brakes' => 2,
            'rapid_accelerations' => 1,
            'speeding_incidents' => 0,
            'score' => 92,
        ]);

        DriverMetric::create([
            'driver_id' => $driver2->id,
            'vehicle_id' => $vehicle2->id,
            'record_date' => Carbon::yesterday(),
            'miles_driven' => 15,
            'fuel_consumed' => 2.0,
            'deliveries_completed' => 4,
            'on_time_percentage' => 85,
            'hard_brakes' => 4,
            'rapid_accelerations' => 3,
            'speeding_incidents' => 1,
            'score' => 78,
        ]);

        // Add more recent metrics for trend data
        DriverMetric::create([
            'driver_id' => $driver1->id,
            'vehicle_id' => $vehicle1->id,
            'record_date' => Carbon::now()->subDays(7),
            'miles_driven' => 25,
            'fuel_consumed' => 3.0,
            'deliveries_completed' => 6,
            'on_time_percentage' => 90,
            'hard_brakes' => 1,
            'rapid_accelerations' => 0,
            'speeding_incidents' => 0,
            'score' => 95,
        ]);

        DriverMetric::create([
            'driver_id' => $driver2->id,
            'vehicle_id' => $vehicle2->id,
            'record_date' => Carbon::now()->subDays(7),
            'miles_driven' => 18,
            'fuel_consumed' => 2.2,
            'deliveries_completed' => 3,
            'on_time_percentage' => 80,
            'hard_brakes' => 3,
            'rapid_accelerations' => 2,
            'speeding_incidents' => 0,
            'score' => 82,
        ]);

        echo "Sample driver performance data seeded successfully!\n";
    }
}

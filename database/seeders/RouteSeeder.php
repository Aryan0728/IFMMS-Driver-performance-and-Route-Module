<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    public function run()
    {
        $drivers = User::where('role', 'Driver')->get();
        $vehicles = Vehicle::all();

        $statuses = ['planned', 'in_progress', 'completed', 'delayed', 'canceled'];

        $routes = [
            [
                'name' => 'Suva to Nadi Delivery',
                'assigned_driver_id' => $drivers[0]->id,
                'vehicle_id' => $vehicles[0]->id,
                'start_time' => Carbon::now()->addDays(1),
                'estimated_end_time' => Carbon::now()->addDays(1)->addHours(4),
                'status' => $statuses[array_rand($statuses)],
                'optimized_waypoints' => json_encode([
                    ['lat' => -18.1401, 'lng' => 178.4186], // Suva
                    ['lat' => -17.8019, 'lng' => 177.4164], // Nadi
                ]),
                'estimated_distance' => 250.0,
                'actual_distance' => null,
                'estimated_duration' => 240, // minutes
                'actual_duration' => null,
                'notes' => 'High priority delivery to western division.',
            ],
            [
                'name' => 'Lautoka Market Run',
                'assigned_driver_id' => $drivers[1]->id,
                'vehicle_id' => $vehicles[1]->id,
                'start_time' => Carbon::now()->addDays(2),
                'estimated_end_time' => Carbon::now()->addDays(2)->addHours(2.5),
                'status' => $statuses[array_rand($statuses)],
                'optimized_waypoints' => json_encode([
                    ['lat' => -17.6167, 'lng' => 177.4500], // Lautoka
                    ['lat' => -17.8019, 'lng' => 177.4164], // Nadi market
                ]),
                'estimated_distance' => 150.0,
                'actual_distance' => null,
                'estimated_duration' => 150,
                'actual_duration' => null,
                'notes' => 'Fresh produce transport.',
            ],
            [
                'name' => 'Nadi to Labasa Freight',
                'assigned_driver_id' => $drivers[2]->id,
                'vehicle_id' => $vehicles[2]->id,
                'start_time' => Carbon::now()->addDays(3),
                'estimated_end_time' => Carbon::now()->addDays(3)->addHours(6),
                'status' => $statuses[array_rand($statuses)],
                'optimized_waypoints' => json_encode([
                    ['lat' => -17.8019, 'lng' => 177.4164], // Nadi
                    ['lat' => -16.4333, 'lng' => 179.6500], // Labasa
                ]),
                'estimated_distance' => 350.0,
                'actual_distance' => null,
                'estimated_duration' => 360,
                'actual_duration' => null,
                'notes' => 'Cross-island freight with multiple stops.',
            ],
            [
                'name' => 'Routine Suva Pickup',
                'assigned_driver_id' => $drivers[0]->id,
                'vehicle_id' => $vehicles[0]->id,
                'start_time' => Carbon::now()->subDays(1),
                'estimated_end_time' => Carbon::now()->subDays(1)->addHours(1.5),
                'status' => 'completed',
                'optimized_waypoints' => json_encode([
                    ['lat' => -18.1401, 'lng' => 178.4186], // Suva start
                    ['lat' => -18.1401, 'lng' => 178.4186], // Local loop
                ]),
                'estimated_distance' => 80.0,
                'actual_distance' => 82.5,
                'estimated_duration' => 90,
                'actual_duration' => 95,
                'notes' => 'Local pickup completed on time.',
            ],
            [
                'name' => 'Emergency Sigatoka Transfer',
                'assigned_driver_id' => $drivers[1]->id,
                'vehicle_id' => $vehicles[1]->id,
                'start_time' => Carbon::now(),
                'estimated_end_time' => Carbon::now()->addHours(3),
                'status' => 'in_progress',
                'optimized_waypoints' => json_encode([
                    ['lat' => -18.1401, 'lng' => 178.4186], // Suva
                    ['lat' => -18.1333, 'lng' => 177.5000], // Sigatoka
                ]),
                'estimated_distance' => 200.0,
                'actual_distance' => null,
                'estimated_duration' => 180,
                'actual_duration' => null,
                'notes' => 'Urgent medical supplies.',
            ],
        ];

        foreach ($routes as $routeData) {
            Route::create($routeData);
        }
    }
}

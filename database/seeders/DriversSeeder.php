<?php
// database/seeders/DriversSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DriverMetric;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriversSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DriverMetric::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $vehicles = Vehicle::all();
        
        $drivers = [
            [
                'name' => 'John Driver',
                'email' => 'john@zar.com',
                'password' => bcrypt('password123'),
                'role' => 'Driver',
                'license_number' => 'DRV123456',
                'hired_date' => '2024-01-15',
                'vehicle_id' => $vehicles->where('license_plate', 'ABC123')->first()->id
            ],
            [
                'name' => 'Sarah Logistics',
                'email' => 'sarah@zar.com',
                'password' => bcrypt('password123'),
                'role' => 'Driver',
                'license_number' => 'DRV789012',
                'hired_date' => '2024-02-20',
                'vehicle_id' => $vehicles->where('license_plate', 'XYZ789')->first()->id
            ],
            [
                'name' => 'Mike Transport',
                'email' => 'mike@zar.com',
                'password' => bcrypt('password123'),
                'role' => 'Driver',
                'license_number' => 'DRV345678',
                'hired_date' => '2024-03-10',
                'vehicle_id' => $vehicles->where('license_plate', 'DEF456')->first()->id
            ]
        ];

        foreach ($drivers as $driverData) {
            $driver = User::create($driverData);
            
            // Create sample metrics for the driver
            for ($i = 0; $i < 30; $i++) {
                DriverMetric::create([
                    'driver_id' => $driver->id,
                    'vehicle_id' => $driver->vehicle_id,
                    'record_date' => Carbon::now()->subDays($i),
                    'miles_driven' => rand(80, 200),
                    'fuel_consumed' => rand(4, 12),
                    'deliveries_completed' => rand(5, 15),
                    'on_time_percentage' => rand(85, 98),
                    'hard_brakes' => rand(0, 3),
                    'rapid_accelerations' => rand(0, 5),
                    'speeding_incidents' => rand(0, 2),
                    'score' => rand(75, 95)
                ]);
            }
        }
        
        // Create an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@zar.com',
            'password' => bcrypt('password123'),
            'role' => 'Admin',
            'license_number' => null,
            'hired_date' => null,
            'vehicle_id' => null
        ]);
    }
}
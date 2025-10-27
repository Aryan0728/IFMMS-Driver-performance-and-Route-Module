<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiclesSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Vehicle::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $vehicles = [
            [
                'make' => 'Toyota',
                'model' => 'Hilux',
                'year' => 2022,
                'license_plate' => 'ABC123',
                'vin' => 'JTELU42N59K000001',
                'fuel_efficiency' => 25.5,
                'odometer' => 45000,
                'last_maintenance_date' => '2024-09-01',
                'status' => 'available',
            ],
            [
                'make' => 'Isuzu',
                'model' => 'D-Max',
                'year' => 2021,
                'license_plate' => 'XYZ789',
                'vin' => 'JALE4RVY8H8000002',
                'fuel_efficiency' => 22.0,
                'odometer' => 32000,
                'last_maintenance_date' => '2024-08-15',
                'status' => 'available',
            ],
            [
                'make' => 'Nissan',
                'model' => 'Navara',
                'year' => 2023,
                'license_plate' => 'DEF456',
                'vin' => 'MNTSN4D59PU000003',
                'fuel_efficiency' => 28.0,
                'odometer' => 18000,
                'last_maintenance_date' => '2024-09-20',
                'status' => 'available',
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            Vehicle::create($vehicleData);
        }
    }
}

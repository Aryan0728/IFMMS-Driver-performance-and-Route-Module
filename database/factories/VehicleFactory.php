<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition()
    {
        return [
            'make' => $this->faker->randomElement(['Toyota', 'Ford', 'Honda', 'Chevrolet', 'Nissan']),
            'model' => $this->faker->word,
            'year' => $this->faker->year(),
            'license_plate' => $this->faker->unique()->bothify('???###'),
            'vin' => $this->faker->unique()->bothify('?#??#########?####'),
            'status' => 'active',
            'current_mileage' => $this->faker->numberBetween(0, 150000),
            'last_maintenance_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'next_maintenance_due' => $this->faker->dateTimeBetween('now', '+6 months')
        ];
    }

    /**
     * Indicate that the vehicle needs maintenance soon.
     */
    public function needsMaintenanceSoon()
    {
        return $this->state(function (array $attributes) {
            return [
                'next_maintenance_due' => now()->addDays(5),
            ];
        });
    }

    /**
     * Indicate that the vehicle's maintenance is overdue.
     */
    public function maintenanceOverdue()
    {
        return $this->state(function (array $attributes) {
            return [
                'next_maintenance_due' => now()->subDays(5),
            ];
        });
    }
}
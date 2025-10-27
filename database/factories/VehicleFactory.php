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
            'make' => $this->faker->company(),
            'model' => $this->faker->word(),
            'year' => $this->faker->year(),
            'license_plate' => strtoupper($this->faker->bothify('???###')),
            'vin' => $this->faker->unique()->regexify('[A-HJ-NPR-Z0-9]{17}'),
            'fuel_efficiency' => $this->faker->randomFloat(2, 10, 30),
            'odometer' => $this->faker->randomFloat(2, 0, 200000),
            'last_maintenance_date' => $this->faker->date(),
            'status' => 'available',
        ];
    }
}

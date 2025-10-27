<?php

namespace Database\Factories;

use App\Models\DriverMetric;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DriverMetricFactory extends Factory
{
    protected $model = DriverMetric::class;

    public function definition()
    {
        return [
            'driver_id' => null, // should be set explicitly
            'vehicle_id' => null, // added vehicle_id to satisfy NOT NULL constraint
            'record_date' => $this->faker->date(),
            'miles_driven' => $this->faker->randomFloat(2, 0, 300),
            'fuel_consumed' => $this->faker->randomFloat(2, 0, 50),
            'deliveries_completed' => $this->faker->numberBetween(0, 20),
            'on_time_percentage' => $this->faker->numberBetween(50, 100),
            'hard_brakes' => $this->faker->numberBetween(0, 10),
            'rapid_accelerations' => $this->faker->numberBetween(0, 10),
            'speeding_incidents' => $this->faker->numberBetween(0, 10),
            'score' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\RouteLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class RouteLogFactory extends Factory
{
    protected $model = RouteLog::class;

    public function definition()
    {
        return [
            'route_id' => null, // should be set explicitly
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'odometer' => $this->faker->randomFloat(2, 0, 1000),
            'speed' => $this->faker->numberBetween(0, 80),
            'fuel_level' => $this->faker->randomFloat(2, 0, 100),
            'location_name' => $this->faker->city(),
            'recorded_at' => Carbon::now(),
        ];
    }
}

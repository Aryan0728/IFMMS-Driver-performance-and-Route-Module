<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'license_number' => $this->faker->unique()->bothify('DL-#####'),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'status' => $this->faker->randomElement(['active', 'inactive', 'on_leave']),
            'hire_date' => $this->faker->dateTimeBetween('-2 years', 'now')
        ];
    }

    /**
     * Indicate that the driver is active.
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }
}
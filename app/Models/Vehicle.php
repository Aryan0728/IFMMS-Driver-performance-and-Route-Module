<?php
// app/Models/Vehicle.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'make', 'model', 'year', 'license_plate', 'vin', 
        'fuel_efficiency', 'odometer', 'last_maintenance_date', 'status'
    ];

    protected $casts = [
        'last_maintenance_date' => 'date',
    ];

    public function driver()
    {
        return $this->hasOne(User::class, 'vehicle_id');
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function metrics()
    {
        return $this->hasMany(DriverMetric::class);
    }
}
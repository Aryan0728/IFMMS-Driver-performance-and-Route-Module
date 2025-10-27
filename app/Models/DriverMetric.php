<?php
// app/Models/DriverMetric.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id', 'vehicle_id', 'record_date', 'miles_driven',
        'fuel_consumed', 'deliveries_completed', 'on_time_percentage',
        'hard_brakes', 'rapid_accelerations', 'speeding_incidents', 'score'
    ];

    protected $casts = [
        'record_date' => 'date',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
<?php
// app/Models/Route.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'assigned_driver_id', 
        'vehicle_id', 
        'start_time',
        'estimated_end_time', 
        'status', 
        'optimized_waypoints',
        'estimated_distance', 
        'actual_distance', 
        'estimated_duration',
        'actual_duration', 
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'estimated_end_time' => 'datetime',
        'optimized_waypoints' => 'array',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function logs()
    {
        return $this->hasMany(RouteLog::class);
    }
}
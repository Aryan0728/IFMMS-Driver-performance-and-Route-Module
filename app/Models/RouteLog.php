<?php
// app/Models/RouteLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'route_id', 'latitude', 'longitude', 'odometer',
        'speed', 'fuel_level', 'location_name', 'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
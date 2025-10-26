<?php

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
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'estimated_end_time' => 'datetime',
        'optimized_waypoints' => 'array',
        'estimated_distance' => 'decimal:2',
        'actual_distance' => 'decimal:2',
        'estimated_duration' => 'decimal:2',
        'actual_duration' => 'decimal:2',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function assignments()
    {
        return $this->hasMany(RouteAssignment::class);
    }

    public function logs()
    {
        return $this->hasMany(RouteLog::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(RouteCheckpoint::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planned', 'in_progress']);
    }

    public function validateWaypoints()
    {
        if (!is_array($this->optimized_waypoints)) {
            return false;
        }
        foreach ($this->optimized_waypoints as $waypoint) {
            if (!isset($waypoint['lat'], $waypoint['lng']) ||
                !is_numeric($waypoint['lat']) || !is_numeric($waypoint['lng']) ||
                $waypoint['lat'] < -90 || $waypoint['lat'] > 90 ||
                $waypoint['lng'] < -180 || $waypoint['lng'] > 180) {
                return false;
            }
        }
        return true;
    }
}
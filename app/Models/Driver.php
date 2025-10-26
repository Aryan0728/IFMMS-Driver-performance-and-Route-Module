<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'license_number',
        'phone',
        'email',
        'status',
        'hire_date'
    ];

    protected $dates = [
        'hire_date'
    ];

    /**
     * Get the user associated with the driver
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the routes assigned to this driver
     */
    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    /**
     * Get the driver's performance records
     */
    public function performanceRecords()
    {
        return $this->hasMany(DriverPerformance::class);
    }

    /**
     * Get the current assigned vehicle
     */
    public function currentVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'current_vehicle_id');
    }

    /**
     * Get the driver's average rating
     */
    public function getAverageRatingAttribute()
    {
        return $this->performanceRecords()->avg('rating') ?? 0;
    }

    /**
     * Get the driver's total completed routes
     */
    public function getTotalCompletedRoutesAttribute()
    {
        return $this->routes()->where('status', 'completed')->count();
    }

    /**
     * Get the driver's total distance covered
     */
    public function getTotalDistanceCoveredAttribute()
    {
        return $this->routes()->where('status', 'completed')->sum('actual_distance');
    }

    /**
     * Scope a query to only include active drivers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if the driver is currently assigned to a route
     */
    public function isAssignedToRoute()
    {
        return $this->routes()->whereIn('status', ['planned', 'in_progress'])->exists();
    }

    /**
     * Get the driver's availability status
     */
    public function getAvailabilityStatusAttribute()
    {
        if ($this->status !== 'active') {
            return 'unavailable';
        }
        
        return $this->isAssignedToRoute() ? 'assigned' : 'available';
    }
}
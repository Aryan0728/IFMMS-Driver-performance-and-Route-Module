<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'license_number',
        'hired_date',
        'vehicle_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'hired_date' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function metrics()
    {
        return $this->hasMany(DriverMetric::class, 'driver_id');
    }

    public function assignedRoutes()
    {
        return $this->hasMany(Route::class, 'assigned_driver_id');
    }

    public function isAdmin()
    {
        return $this->role === 'Admin';
    }

    public function isDriver()
    {
        return $this->role === 'Driver';
    }

    public function isTechnician()
    {
        return $this->role === 'Technician';
    }
}
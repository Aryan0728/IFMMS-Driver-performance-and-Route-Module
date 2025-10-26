<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'metric_date',
        'total_distance',
        'total_driving_time',
        'fuel_efficiency',
        'average_speed',
        'routes_completed',
        'routes_assigned',
        'on_time_percentage',
        'safety_incidents',
        'traffic_violations',
        'customer_rating',
        'deliveries_completed',
        'deliveries_failed',
        'overtime_hours',
        'idle_time',
    ];

    protected $casts = [
        'metric_date' => 'date',
        'total_distance' => 'decimal:2',
        'total_driving_time' => 'decimal:2',
        'fuel_efficiency' => 'decimal:2',
        'average_speed' => 'decimal:2',
        'on_time_percentage' => 'decimal:2',
        'customer_rating' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'idle_time' => 'decimal:2',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public static function calculateMetricsForDriver($driverId, $date)
    {
        $assignments = RouteAssignment::where('driver_id', $driverId)
            ->whereDate('assignment_date', $date)
            ->where('status', 'completed')
            ->get();

        $totalDistance = $assignments->sum('actual_distance');
        $totalDrivingTime = $assignments->sum('actual_duration');
        $fuelEfficiency = $assignments->sum('fuel_consumed') > 0
            ? round($totalDistance / $assignments->sum('fuel_consumed'), 2)
            : 0;
        $averageSpeed = $totalDrivingTime > 0
            ? round(($totalDistance / $totalDrivingTime) * 60, 2)
            : 0;
        $routesCompleted = $assignments->count();
        $onTimeCount = $assignments->filter(fn($a) => $a->is_on_time)->count();
        $onTimePercentage = $routesCompleted > 0
            ? round(($onTimeCount / $routesCompleted) * 100, 2)
            : 0;

        return [
            'total_distance' => $totalDistance,
            'total_driving_time' => $totalDrivingTime,
            'fuel_efficiency' => $fuelEfficiency,
            'average_speed' => $averageSpeed,
            'routes_completed' => $routesCompleted,
            'on_time_percentage' => $onTimePercentage,
        ];
    }

    public function scopeByPeriod($query, $period, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->startOfDay();
        switch ($period) {
            case 'daily':
                return $query->whereDate('metric_date', $startDate);
            case 'weekly':
                return $query->whereBetween('metric_date', [
                    $startDate->startOfWeek(),
                    $startDate->endOfWeek(),
                ]);
            case 'monthly':
                return $query->whereMonth('metric_date', $startDate->month)
                            ->whereYear('metric_date', $startDate->year);
            case 'custom':
                return $query->whereBetween('metric_date', [$startDate, $endDate]);
            default:
                return $query;
        }
    }
}
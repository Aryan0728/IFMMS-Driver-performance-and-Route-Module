<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'period_start',
        'period_end',
        'period_type',
        'total_distance',
        'total_routes',
        'average_fuel_efficiency',
        'average_speed',
        'on_time_percentage',
        'safety_score',
        'customer_rating',
        'performance_score',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_distance' => 'decimal:2',
        'average_fuel_efficiency' => 'decimal:2',
        'average_speed' => 'decimal:2',
        'on_time_percentage' => 'decimal:2',
        'safety_score' => 'decimal:2',
        'customer_rating' => 'decimal:2',
        'performance_score' => 'decimal:2',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public static function generatePerformance($driverId, $periodType, $startDate, $endDate = null)
    {
        $metrics = DriverMetric::where('driver_id', $driverId)
            ->byPeriod($periodType, $startDate, $endDate)
            ->get();

        if ($metrics->isEmpty()) {
            return null;
        }

        $totalDistance = $metrics->sum('total_distance');
        $totalRoutes = $metrics->sum('routes_completed');
        $averageFuelEfficiency = $metrics->avg('fuel_efficiency');
        $averageSpeed = $metrics->avg('average_speed');
        $onTimePercentage = $metrics->avg('on_time_percentage');
        $safetyScore = 100 - ($metrics->sum('safety_incidents') * 5 + $metrics->sum('traffic_violations') * 10);
        $customerRating = $metrics->avg('customer_rating');
        $performanceScore = round(
            ($onTimePercentage * 0.4) +
            ($safetyScore * 0.3) +
            ($customerRating * 20 * 0.2) +
            ($averageFuelEfficiency * 0.1),
            2
        );

        return self::updateOrCreate(
            [
                'driver_id' => $driverId,
                'period_start' => $startDate,
                'period_end' => $endDate ?? $startDate,
                'period_type' => $periodType,
            ],
            [
                'total_distance' => $totalDistance,
                'total_routes' => $totalRoutes,
                'average_fuel_efficiency' => $averageFuelEfficiency,
                'average_speed' => $averageSpeed,
                'on_time_percentage' => $onTimePercentage,
                'safety_score' => max(0, $safetyScore),
                'customer_rating' => $customerRating,
                'performance_score' => $performanceScore,
            ]
        );
    }

    public function scopeRanked($query, $periodType, $startDate, $endDate = null)
    {
        return $query->where('period_type', $periodType)
            ->where('period_start', $startDate)
            ->when($endDate, fn($q) => $q->where('period_end', $endDate))
            ->orderBy('performance_score', 'desc');
    }
}
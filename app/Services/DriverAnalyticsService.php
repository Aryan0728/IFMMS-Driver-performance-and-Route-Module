<?php
// app/Services/DriverAnalyticsService.php

namespace App\Services;

use App\Models\RouteLog;
use App\Models\DriverMetric;
use Carbon\Carbon;

class DriverAnalyticsService
{
    /**
     * Analyze route logs for a driver and update driver metrics with behavior analytics.
     * 
     * @param int $driverId
     * @param Carbon $date
     * @return DriverMetric|null
     */
    public function analyzeDriverBehavior(int $driverId, Carbon $date)
    {
        // Get the driver to access vehicle_id
        $driver = \App\Models\User::find($driverId);

        // Get all route logs for the driver on the given date
        $logs = RouteLog::whereHas('route', function ($query) use ($driverId) {
            $query->where('assigned_driver_id', $driverId);
        })
        ->whereDate('recorded_at', $date)
        ->orderBy('recorded_at')
        ->get();

        if ($logs->isEmpty()) {
            return null;
        }

        $hardBrakes = 0;
        $rapidAccelerations = 0;
        $speedingIncidents = 0;
        $idlingTimeSeconds = 0;
        $milesDriven = $logs->last()->odometer - $logs->first()->odometer;
        $fuelConsumed = $logs->first()->fuel_level - $logs->last()->fuel_level;
        $deliveriesCompleted = 0; // Placeholder, can be calculated from routes
        $onTimePercentage = 100; // Placeholder

        $previousLog = null;
        $idlingStart = null;

        foreach ($logs as $log) {
            if ($previousLog) {
                $timeDiff = $log->recorded_at->diffInSeconds($previousLog->recorded_at);
                $speedDiff = $log->speed - $previousLog->speed;

                // Detect harsh braking: speed decrease > 10 mph within 1 second
                if ($speedDiff < -10 && $timeDiff <= 1) {
                    $hardBrakes++;
                }

                // Detect rapid acceleration: speed increase > 10 mph within 1 second
                if ($speedDiff > 10 && $timeDiff <= 1) {
                    $rapidAccelerations++;
                }

                // Detect idling: speed == 0 for more than 5 minutes
                if ($log->speed == 0) {
                    if ($idlingStart === null) {
                        $idlingStart = $log->recorded_at;
                    }
                } else {
                    if ($idlingStart !== null) {
                        $idlingDuration = $log->recorded_at->diffInSeconds($idlingStart);
                        if ($idlingDuration >= 300) { // 5 minutes
                            $idlingTimeSeconds += $idlingDuration;
                        }
                        $idlingStart = null;
                    }
                }

                // Detect speeding: speed > 70 mph
                if ($log->speed > 70) {
                    $speedingIncidents++;
                }
            } else {
                // First log, check speeding
                if ($log->speed > 70) {
                    $speedingIncidents++;
                }
                if ($log->speed == 0) {
                    $idlingStart = $log->recorded_at;
                }
            }

            $previousLog = $log;
        }

        // If idling at end of logs
        if ($idlingStart !== null) {
            $idlingDuration = $previousLog->recorded_at->diffInSeconds($idlingStart);
            if ($idlingDuration >= 300) {
                $idlingTimeSeconds += $idlingDuration;
            }
        }

        // Calculate composite score (simple example)
        $score = 100;
        $score -= $hardBrakes * 2;
        $score -= $rapidAccelerations * 1.5;
        $score -= $speedingIncidents * 3;
        $score -= ($idlingTimeSeconds / 60) * 0.5; // penalty per minute idling
        $score = max(0, min(100, $score));

        // Update or create driver metric for the date
        $metric = DriverMetric::updateOrCreate(
            ['driver_id' => $driverId, 'record_date' => $date->toDateString()],
            [
                'vehicle_id' => $driver->vehicle_id,
                'total_distance' => $milesDriven,
                'fuel_consumed' => $fuelConsumed,
                'deliveries_completed' => $deliveriesCompleted,
                'on_time_percentage' => $onTimePercentage,
                'safety_incidents' => $hardBrakes,
                'traffic_violations' => $speedingIncidents,
                'idle_time' => $idlingTimeSeconds / 3600, // Convert to hours
                // Other fields can be updated elsewhere
            ]
        );

        return $metric;
    }
}

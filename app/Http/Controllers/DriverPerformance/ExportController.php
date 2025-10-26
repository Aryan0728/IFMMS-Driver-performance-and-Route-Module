<?php

namespace App\Http\Controllers\DriverPerformance;

use App\Http\Controllers\Controller;
use App\Models\RouteAssignment;
use App\Models\DriverPerformanceMetric;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function exportRoutes(Request $request)
    {
        $format = $request->get('format', 'csv');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $driverId = $request->get('driver_id');
        $status = $request->get('status');

        $query = \App\Models\Route::with(['driver', 'vehicle'])
            ->when($startDate, fn($q) => $q->whereDate('start_time', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('start_time', '<=', $endDate))
            ->when($driverId, fn($q) => $q->where('assigned_driver_id', $driverId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderBy('start_time', 'desc');

        $filename = 'routes_' . now()->format('Ymd_His');

        if ($format === 'csv') {
            $filename .= '.csv';
            return response()->streamDownload(function () use ($query) {
                $out = fopen('php://output', 'w');
                fputcsv($out, [
                    'ID', 'Route Name', 'Driver', 'Vehicle', 'Start Time',
                    'Estimated End Time', 'Status', 'Estimated Distance',
                    'Estimated Duration', 'Notes', 'Created At'
                ]);

                $query->chunk(100, function ($routes) use ($out) {
                    foreach ($routes as $route) {
                        fputcsv($out, [
                            $route->id,
                            $route->name ?? 'N/A',
                            $route->driver->name ?? 'N/A',
                            $route->vehicle ? ($route->vehicle->make . ' ' . $route->vehicle->model) : 'N/A',
                            $route->start_time,
                            $route->estimated_end_time,
                            $route->status,
                            $route->estimated_distance,
                            $route->estimated_duration,
                            $route->notes,
                            $route->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }
                });
                fclose($out);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        return response()->json(['error' => 'Unsupported format'], 400);
    }

    public function exportPerformance(Request $request)
    {
        $format = $request->get('format', 'csv');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $driverId = $request->get('driver_id');

        $query = \App\Models\DriverMetric::with('driver')
            ->when($startDate, fn($q) => $q->whereDate('record_date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('record_date', '<=', $endDate))
            ->when($driverId, fn($q) => $q->where('driver_id', $driverId))
            ->orderBy('record_date', 'desc');

        $filename = 'driver_performance_' . now()->format('Ymd_His');

        if ($format === 'csv') {
            $filename .= '.csv';
            return response()->streamDownload(function () use ($query) {
                $out = fopen('php://output', 'w');
                fputcsv($out, [
                    'ID', 'Driver', 'Record Date', 'Miles Driven', 'Fuel Consumed',
                    'Deliveries Completed', 'On-Time Percentage', 'Hard Brakes',
                    'Rapid Accelerations', 'Speeding Incidents', 'Score'
                ]);

                $query->chunk(100, function ($metrics) use ($out) {
                    foreach ($metrics as $metric) {
                        fputcsv($out, [
                            $metric->id,
                            $metric->driver->name ?? 'N/A',
                            $metric->record_date,
                            $metric->miles_driven,
                            $metric->fuel_consumed,
                            $metric->deliveries_completed,
                            $metric->on_time_percentage,
                            $metric->hard_brakes,
                            $metric->rapid_accelerations,
                            $metric->speeding_incidents,
                            $metric->score,
                        ]);
                    }
                });
                fclose($out);
            }, $filename, ['Content-Type' => 'text/csv']);
        }

        return response()->json(['error' => 'Unsupported format'], 400);
    }
}

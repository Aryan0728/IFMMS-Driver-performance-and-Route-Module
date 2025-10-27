@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Driver Performance Dashboard</h1>
        <span class="badge bg-secondary">Last {{ $timePeriod }}</span>
    </div>
    
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Average Score</h5>
                    <h2>{{ number_format($overallMetrics->avg_score, 1) }}/100</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">On-Time %</h5>
                    <h2>{{ number_format($overallMetrics->on_time_avg, 1) }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Total Miles</h5>
                    <h2>{{ number_format($overallMetrics->total_miles) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">Fuel Consumed</h5>
                    <h2>{{ number_format($overallMetrics->total_fuel) }} gal</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Driver Performance Overview</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Driver</th>
                            <th>Vehicle</th>
                            <th>Avg Score</th>
                            <th>Total Miles</th>
                            <th>On-Time %</th>
                            <th>Fuel Efficiency</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $driver)
                        @php
                            $metrics = $driver->metrics;
                            $totalMiles = $metrics->sum('miles_driven');
                            $totalFuel = $metrics->sum('fuel_consumed');
                            $fuelEfficiency = $totalFuel > 0 ? $totalMiles / $totalFuel : 0;
                        @endphp
                        <tr>
                            <td>{{ $driver->name }}</td>
                            <td>
                                @if($driver->vehicle)
                                    {{ $driver->vehicle->make }} {{ $driver->vehicle->model }} ({{ $driver->vehicle->license_plate }})
                                @else
                                    <span class="text-muted">No vehicle assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $metrics->avg('score') >= 90 ? 'success' : ($metrics->avg('score') >= 80 ? 'warning' : 'danger') }}">
                                    {{ number_format($metrics->avg('score'), 1) }}
                                </span>
                            </td>
                            <td>{{ number_format($totalMiles) }}</td>
                            <td>{{ number_format($metrics->avg('on_time_percentage'), 1) }}%</td>
                            <td>{{ number_format($fuelEfficiency, 1) }} MPG</td>
                            <td>
                                @if($driver->isDriver())
                                <a href="{{ route('driver-performance.show', $driver) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
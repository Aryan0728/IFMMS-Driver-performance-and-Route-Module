@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Driver Details: {{ $driver->name }}</h1>
        <div>
            @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'Technician')
                <a href="{{ route('driver-performance.edit', $driver->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit Driver
                </a>
            @endif
            <a href="{{ route('driver-performance.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Performance
            </a>
        </div>
    </div>

    <!-- Driver Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Driver Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ $driver->name }}</p>
                    <p><strong>Email:</strong> {{ $driver->email }}</p>
                    <p><strong>License Number:</strong> {{ $driver->license_number ?? 'Not provided' }}</p>
                    <p><strong>License Expiry:</strong> {{ $driver->license_expiry ? $driver->license_expiry->format('M d, Y') : 'Not provided' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ $driver->status === 'active' ? 'success' : ($driver->status === 'inactive' ? 'secondary' : 'warning') }}">
                            {{ ucfirst($driver->status) }}
                        </span>
                    </p>
                    <p><strong>Phone:</strong> {{ $driver->phone ?? 'Not provided' }}</p>
                    <p><strong>Emergency Contact:</strong> {{ $driver->emergency_contact ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    @if($metrics && $metrics->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">Recent Performance Metrics</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Miles Driven</th>
                                <th>Fuel Consumed</th>
                                <th>Deliveries Completed</th>
                                <th>On-Time %</th>
                                <th>Hard Brakes</th>
                                <th>Rapid Accelerations</th>
                                <th>Speeding Incidents</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($metrics as $metric)
                            <tr>
                                <td>{{ $metric->record_date->format('M d, Y') }}</td>
                                <td>{{ number_format($metric->miles_driven, 1) }}</td>
                                <td>{{ number_format($metric->fuel_consumed, 1) }}</td>
                                <td>{{ $metric->deliveries_completed }}</td>
                                <td>{{ number_format($metric->on_time_percentage, 1) }}%</td>
                                <td>{{ $metric->hard_brakes }}</td>
                                <td>{{ $metric->rapid_accelerations }}</td>
                                <td>{{ $metric->speeding_incidents }}</td>
                                <td>{{ $metric->score }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Assignments -->
    @if($recentAssignments && $recentAssignments->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">Recent Route Assignments</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Route</th>
                                <th>Vehicle</th>
                                <th>Assignment Date</th>
                                <th>Status</th>
                                <th>Actual Distance</th>
                                <th>Actual Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAssignments as $assignment)
                            <tr>
                                <td>{{ $assignment->route->name ?? 'N/A' }}</td>
                                <td>{{ $assignment->vehicle->vehicle_number ?? 'N/A' }}</td>
                                <td>{{ $assignment->assignment_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $assignment->status === 'completed' ? 'success' : ($assignment->status === 'in_progress' ? 'primary' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                    </span>
                                </td>
                                <td>{{ $assignment->actual_distance ? number_format($assignment->actual_distance, 1) . ' miles' : 'N/A' }}</td>
                                <td>{{ $assignment->actual_duration ? $assignment->actual_duration . ' min' : 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-secondary text-white">
            <h6 class="m-0 font-weight-bold">Actions</h6>
        </div>
        <div class="card-body">
            <div class="row">
                @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'Technician')
                    <div class="col-md-3">
                        <a href="{{ route('driver-performance.edit', $driver->id) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-edit"></i> Edit Driver
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('driver-performance.analyze-behavior', $driver->id) }}" class="btn btn-info btn-block">
                            <i class="fas fa-chart-line"></i> Analyze Behavior
                        </a>
                    </div>
                @endif
                <div class="col-md-3">
                    <a href="{{ route('driver-performance.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
                @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'Technician')
                    <div class="col-md-3">
                        <a href="{{ route('driver-performance.rankings') }}" class="btn btn-outline-success btn-block">
                            <i class="fas fa-trophy"></i> View Rankings
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

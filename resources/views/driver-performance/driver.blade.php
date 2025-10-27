@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Driver: {{ $driver->name }}</h1>
        <div>
            <form action="{{ route('driver-performance.analyze-behavior', $driver) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-info btn-sm shadow-sm">
                    <i class="fas fa-chart-line fa-sm text-white-50"></i> Analyze Behavior
                </button>
            </form>
            <a href="{{ route('driver-performance.dashboard') }}" class="btn btn-secondary btn-sm shadow-sm ml-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
            </a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Driver Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p><strong>Name:</strong> {{ $driver->name }}</p>
                            <p><strong>Email:</strong> {{ $driver->email }}</p>
                            <p><strong>License Number:</strong> {{ $driver->license_number }}</p>
                        </div>
                        <div class="col-6">
                            <p><strong>Hire Date:</strong> {{ $driver->hired_date ? $driver->hired_date->format('M d, Y') : 'N/A' }}</p>
                            <p><strong>Vehicle:</strong>
                                @if($driver->vehicle)
                                    {{ $driver->vehicle->make }} {{ $driver->vehicle->model }}
                                @else
                                    <span class="text-muted">No vehicle assigned</span>
                                @endif
                            </p>
                            <p><strong>License Plate:</strong>
                                @if($driver->vehicle)
                                    {{ $driver->vehicle->license_plate }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">Performance Summary (Last 30 Days)</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Average Score</strong></p>
                            <h3 class="text-{{ $metrics->avg('score') >= 90 ? 'success' : ($metrics->avg('score') >= 80 ? 'warning' : 'danger') }}">
                                {{ number_format($metrics->avg('score'), 1) }}/100
                            </h3>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>On-Time %</strong></p>
                            <h3 class="text-{{ $metrics->avg('on_time_percentage') >= 95 ? 'success' : ($metrics->avg('on_time_percentage') >= 90 ? 'warning' : 'danger') }}">
                                {{ number_format($metrics->avg('on_time_percentage'), 1) }}%
                            </h3>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-1"><strong>Total Miles</strong></p>
                            <h3 class="text-info">{{ number_format($metrics->sum('miles_driven')) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">Training Recommendations</h6>
                </div>
                <div class="card-body">
                    @if(!empty($recommendations))
                        <ul class="list-unstyled">
                            @foreach($recommendations as $rec)
                                <li class="mb-2">
                                    <i class="fas fa-exclamation-triangle text-warning"></i> {{ $rec }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-success mb-0">
                            <i class="fas fa-check-circle"></i> No training recommendations at this time.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-info text-white">
            <h6 class="m-0 font-weight-bold">Daily Performance Metrics</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Score</th>
                            <th>Miles</th>
                            <th>Fuel (gal)</th>
                            <th>Deliveries</th>
                            <th>On-Time %</th>
                            <th>Hard Brakes</th>
                            <th>Rapid Accel</th>
                            <th>Speeding</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($metrics as $metric)
                        <tr>
                            <td>{{ $metric->record_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $metric->score >= 90 ? 'success' : ($metric->score >= 80 ? 'warning' : 'danger') }}">
                                    {{ $metric->score }}
                                </span>
                            </td>
                            <td>{{ $metric->miles_driven }}</td>
                            <td>{{ $metric->fuel_consumed }}</td>
                            <td>{{ $metric->deliveries_completed }}</td>
                            <td>{{ $metric->on_time_percentage }}%</td>
                            <td>{{ $metric->hard_brakes }}</td>
                            <td>{{ $metric->rapid_accelerations }}</td>
                            <td>{{ $metric->speeding_incidents }}</td>
                            <td>
                                <a href="{{ route('driver-performance.edit', [$driver, $metric]) }}" class="btn btn-sm btn-warning shadow-sm">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($metrics->hasPages())
                {{ $metrics->links('vendor.pagination.no-arrows') }}
            @endif
        </div>
    </div>
</div>
@endsection

<style>
.pagination {
    font-size: 0.75rem;
    justify-content: center;
    margin-bottom: 0;
}
.pagination .page-link {
    padding: 0.25rem 0.5rem;
    margin: 0 0.125rem;
}
</style>

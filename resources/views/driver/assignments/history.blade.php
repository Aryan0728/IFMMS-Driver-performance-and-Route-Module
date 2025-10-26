@extends('layouts.app')

@section('title', 'Route History - IFMMS-ZAR')

@section('content')
<div class="page-header">
    <h1 class="page-title">Route History</h1>
    <p class="page-subtitle">View your completed and cancelled routes</p>
</div>

<!-- Performance Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-gradient-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['total_completed'] }}</h4>
                        <p class="mb-0">Total Completed</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-gradient-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ number_format($stats['total_distance'], 1) }} km</h4>
                        <p class="mb-0">Total Distance</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-road fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-gradient-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['avg_completion_time'] ? number_format($stats['avg_completion_time']) . ' min' : 'N/A' }}</h4>
                        <p class="mb-0">Avg. Completion Time</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['on_time_percentage'] }}%</h4>
                        <p class="mb-0">On-Time Performance</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tachometer-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Route History</h5>
                
                <!-- Filters -->
                <form method="GET" class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-control" id="month" name="month">
                            <option value="">All Months</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-control" id="year" name="year">
                            <option value="">All Years</option>
                            @for($year = now()->year; $year >= now()->year - 2; $year--)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('driver.assignments.history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                @if($assignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Route</th>
                                    <th>Vehicle</th>
                                    <th>Duration</th>
                                    <th>Distance</th>
                                    <th>Status</th>
                                    <th>Performance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                <tr>
                                    <td>
                                        <strong>{{ $assignment->assignment_date->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $assignment->assignment_date->format('l') }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $assignment->route->route_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $assignment->route->route_code }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $assignment->vehicle->vehicle_number }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $assignment->vehicle->make }} {{ $assignment->vehicle->model }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($assignment->actual_duration)
                                            <strong>{{ $assignment->actual_duration }} min</strong>
                                            <br>
                                            <small class="text-muted">Est: {{ $assignment->route->estimated_duration }} min</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignment->actual_distance)
                                            <strong>{{ number_format($assignment->actual_distance, 1) }} km</strong>
                                            <br>
                                            <small class="text-muted">Est: {{ number_format($assignment->route->total_distance, 1) }} km</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status === 'completed' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($assignment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($assignment->status === 'completed')
                                            @if($assignment->is_on_time)
                                                <span class="badge bg-success">On Time</span>
                                            @else
                                                <span class="badge bg-warning">Late</span>
                                                <br>
                                                <small class="text-muted">{{ $assignment->delay_minutes }} min delay</small>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('driver.assignments.show', $assignment) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $assignments->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No route history found</h5>
                        <p class="text-muted">Complete some routes to see your history here.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
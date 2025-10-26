@extends('layouts.app')

@section('title', 'My Schedule - IFMMS-ZAR')

@section('content')
<div class="page-header">
    <h1 class="page-title">My Schedule</h1>
    <p class="page-subtitle">View all your route assignments</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['total_assignments'] }}</h4>
                        <p class="mb-0">Total Assignments</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-route fa-2x"></i>
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
                        <h4 class="mb-0">{{ $stats['this_week'] }}</h4>
                        <p class="mb-0">This Week</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-week fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-gradient-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $stats['completed_this_month'] }}</h4>
                        <p class="mb-0">Completed This Month</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
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
                        <h4 class="mb-0">{{ $stats['upcoming'] }}</h4>
                        <p class="mb-0">Upcoming</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
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
                <h5 class="card-title">Route Assignments</h5>
                
                <!-- Filters -->
                <form method="GET" class="row g-3 mt-3">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('driver.assignments.index') }}" class="btn btn-outline-secondary">
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
                                    <th>Time</th>
                                    <th>Status</th>
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
                                        <div>
                                            <strong>{{ $assignment->scheduled_start_time }} - {{ $assignment->scheduled_end_time }}</strong>
                                            @if($assignment->actual_start_time)
                                                <br>
                                                <small class="text-success">Started: {{ $assignment->actual_start_time }}</small>
                                            @endif
                                            @if($assignment->actual_end_time)
                                                <br>
                                                <small class="text-info">Ended: {{ $assignment->actual_end_time }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assignment->status === 'assigned' ? 'primary' : ($assignment->status === 'in_progress' ? 'warning' : ($assignment->status === 'completed' ? 'success' : 'secondary')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                        </span>
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
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No assignments found</h5>
                        <p class="text-muted">Try adjusting your filters or check back later for new assignments.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
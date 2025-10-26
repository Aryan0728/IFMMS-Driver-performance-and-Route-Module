@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route(strtolower(auth()->user()->role).'.dashboard') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 class="h3 mb-0 text-gray-800 d-inline">
                <i class="fas fa-clipboard-list text-primary"></i> Route Assignments
            </h1>
        </div>
        <div>
            @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'Technician')
            <a href="{{ route('driver-performance.export.routes') }}" class="btn btn-outline-success btn-sm me-2">
                <i class="fas fa-download"></i> Export Routes
            </a>
            @endif
            <a href="{{ route('route-assignments.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Create New Assignment
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Assignments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_assignments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Today's Assignments
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today_assignments'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Completed Today
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_today'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                In Progress
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['in_progress'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filters & Search
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('route-assignments.index') }}" class="row">
                <div class="col-md-3 mb-2">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by route name...">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Driver</label>
                    <select name="driver_id" class="form-control">
                        <option value="">All Drivers</option>
                        @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter fa-sm"></i> Apply Filters
                    </button>
                    <a href="{{ route('route-assignments.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync fa-sm"></i> Clear All
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs" id="routeAssignmentsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="button" role="tab" aria-controls="assignments" aria-selected="true">
                <i class="fas fa-clipboard-list"></i> Assignments
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="gps-tab" data-bs-toggle="tab" data-bs-target="#gps" type="button" role="tab" aria-controls="gps" aria-selected="false">
                <i class="fas fa-map-marked-alt"></i> GPS Tracking
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="routeAssignmentsTabContent">
        <!-- Assignments Tab -->
        <div class="tab-pane fade show active" id="assignments" role="tabpanel" aria-labelledby="assignments-tab">
            <!-- Assignments Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-list"></i> All Route Assignments
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Route</th>
                                    <th>Driver</th>
                                    <th>Vehicle</th>
                                    <th>Assignment Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                <tr>
                                    <td>
                                        <strong>{{ $assignment->route->route_name ?? 'N/A' }}</strong>
                                        @if($assignment->notes)
                                        <br><small class="text-muted">{{ Str::limit($assignment->notes, 30) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignment->driver)
                                            {{ $assignment->driver->name }}
                                        @else
                                            <span class="text-muted">No driver</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignment->vehicle)
                                            {{ $assignment->vehicle->make }} {{ $assignment->vehicle->model }}
                                            <br><small class="text-muted">{{ $assignment->vehicle->license_plate }}</small>
                                        @else
                                            <span class="text-muted">No vehicle</span>
                                        @endif
                                    </td>
                                    <td>{{ $assignment->assignment_date->format('M d, Y') }}</td>
                                    <td>{{ $assignment->scheduled_start_time }} - {{ $assignment->scheduled_end_time }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'assigned' => 'secondary',
                                                'in_progress' => 'primary',
                                                'completed' => 'success',
                                                'cancelled' => 'danger'
                                            ];
                                            $statusIcons = [
                                                'assigned' => 'calendar',
                                                'in_progress' => 'play-circle',
                                                'completed' => 'check-circle',
                                                'cancelled' => 'times-circle'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$assignment->status] ?? 'secondary' }}">
                                            <i class="fas fa-{{ $statusIcons[$assignment->status] ?? 'calendar' }} fa-sm me-1"></i>
                                            {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('route-assignments.show', $assignment) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($assignment->status === 'assigned')
                                        <a href="{{ route('route-assignments.edit', $assignment) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('route-assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No route assignments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($assignments->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $assignments->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- GPS Tracking Tab -->
        <div class="tab-pane fade" id="gps" role="tabpanel" aria-labelledby="gps-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map-marked-alt"></i> GPS Tracking of Vehicles
                    </h6>
                </div>
                <div class="card-body">
                    <div id="assignment-map" style="height: 400px; width: 100%; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
                    <div class="mt-3">
                        <h6>Active Assignments</h6>
                        <ul id="assignment-list" class="list-group">
                            <!-- Assignment items will be dynamically loaded here -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#assignment-map {
    position: relative;
    overflow: hidden !important;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.leaflet-container {
    height: 100% !important;
    width: 100% !important;
    position: relative !important;
}
.leaflet-map-pane,
.leaflet-tile-pane,
.leaflet-objects-pane,
.leaflet-tile,
.leaflet-marker-icon,
.leaflet-marker-shadow,
.leaflet-popup,
.leaflet-control-container {
    position: absolute !important;
}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
$(document).ready(function() {
    // Auto-submit form on filter change
    $('select[name="status"], select[name="driver_id"]').change(function() {
        $(this).closest('form').submit();
    });

    // Initialize map when GPS tab is shown
    $('button[data-bs-target="#gps"]').on('shown.bs.tab', function (e) {
        initializeAssignmentMap();
    });
});

function initializeAssignmentMap() {
    if (typeof window.assignmentMap !== 'undefined') {
        return; // Map already initialized
    }

    // Initialize map centered on Fiji
    window.assignmentMap = L.map('assignment-map').setView([-17.7134, 178.0650], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(window.assignmentMap);

    // Load vehicles and active routes
    loadAssignmentMapData();
}

function loadAssignmentMapData() {
    // Load vehicles
    $.get('/route-management/vehicles')
        .done(function(vehicles) {
            vehicles.forEach(function(vehicle) {
                var marker = L.marker([vehicle.lat, vehicle.lng])
                    .addTo(window.assignmentMap)
                    .bindPopup('<strong>' + vehicle.name + '</strong><br>Driver: ' + vehicle.driver);

                // Add to assignment list
                $('#assignment-list').append(
                    '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                        vehicle.name +
                        '<span class="badge bg-primary rounded-pill">Vehicle</span>' +
                    '</li>'
                );
            });
        })
        .fail(function() {
            console.error('Failed to load vehicles');
        });

    // Load active routes
    $.get('/route-management/active-routes')
        .done(function(routes) {
            routes.forEach(function(route) {
                if (route.optimized_waypoints && route.optimized_waypoints.length > 0) {
                    var coords = route.optimized_waypoints.map(function(wp) {
                        return [wp.lat, wp.lng];
                    });

                    // Add route polyline
                    L.polyline(coords, {color: 'blue', weight: 3, opacity: 0.7}).addTo(window.assignmentMap);

                    // Add route to assignment list
                    $('#assignment-list').append(
                        '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                            route.name +
                            '<span class="badge bg-info rounded-pill">' + route.status + '</span>' +
                        '</li>'
                    );
                }
            });
        })
        .fail(function() {
            console.error('Failed to load active routes');
        });
}
</script>
@endpush

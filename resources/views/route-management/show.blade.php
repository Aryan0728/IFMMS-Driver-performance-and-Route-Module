@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-route text-primary"></i> Route: {{ $route->name }}
        </h1>
        <div>
            <span class="badge bg-{{
                $route->status === 'completed' ? 'success' :
                ($route->status === 'in_progress' ? 'primary' :
                ($route->status === 'delayed' ? 'warning' :
                ($route->status === 'canceled' ? 'danger' : 'secondary')))
            }} me-2 fs-6">
                <i class="fas fa-{{
                    $route->status === 'completed' ? 'check-circle' :
                    ($route->status === 'in_progress' ? 'play-circle' :
                    ($route->status === 'delayed' ? 'clock' :
                    ($route->status === 'canceled' ? 'times-circle' : 'calendar')))
                }}"></i> {{ ucfirst(str_replace('_', ' ', $route->status)) }}
            </span>
            <a href="{{ route('route-management.edit', $route) }}" class="btn btn-warning btn-sm shadow-sm me-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Route
            </a>
            <a href="{{ route('route-management.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Routes
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle"></i> Route Details
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="mb-2">
                                <i class="fas fa-user text-primary me-2"></i>
                                <strong>Driver:</strong><br>
                                <span class="text-muted">{{ $route->driver->name }}</span>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-truck text-primary me-2"></i>
                                <strong>Vehicle:</strong><br>
                                <span class="text-muted">{{ $route->vehicle->make }} {{ $route->vehicle->model }}</span>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-hashtag text-primary me-2"></i>
                                <strong>License Plate:</strong><br>
                                <span class="text-muted">{{ $route->vehicle->license_plate }}</span>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <p class="mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Start Time:</strong><br>
                                <span class="text-muted">{{ $route->start_time->format('M d, Y H:i') }}</span>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <strong>Estimated End:</strong><br>
                                <span class="text-muted">{{ $route->estimated_end_time->format('M d, Y H:i') }}</span>
                            </p>
                            <p class="mb-2">
                                <i class="fas fa-hourglass-half text-primary me-2"></i>
                                <strong>Duration:</strong><br>
                                <span class="text-muted">{{ $route->estimated_duration }} minutes</span>
                            </p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <p class="mb-2">
                                <i class="fas fa-road text-primary me-2"></i>
                                <strong>Distance:</strong><br>
                                <span class="text-muted">{{ $route->estimated_distance }} miles</span>
                            </p>
                        </div>
                        <div class="col-sm-6">
                            @if($route->notes)
                            <p class="mb-0">
                                <i class="fas fa-sticky-note text-primary me-2"></i>
                                <strong>Notes:</strong><br>
                                <span class="text-muted">{{ $route->notes }}</span>
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-map-marked-alt"></i> Route Map Preview
                    </h6>
                </div>
                <div class="card-body">
                    <div id="routeMap" style="height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; color: white;">
                        <div class="text-center">
                            <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                            <h6>Interactive Route Map</h6>
                            <p class="mb-0 small">Map visualization loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-success shadow">
                <div class="card-header py-3 bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-map-marker-alt"></i> Route Waypoints
                    </h6>
                    <span class="badge bg-light text-dark">
                        {{ $route->optimized_waypoints ? count($route->optimized_waypoints) : 0 }} Stops
                    </span>
                </div>
                <div class="card-body">
                    @if($route->optimized_waypoints && is_array($route->optimized_waypoints) && count($route->optimized_waypoints) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-hashtag text-muted"></i> #</th>
                                    <th><i class="fas fa-map-pin text-muted"></i> Location Name</th>
                                    <th><i class="fas fa-globe text-muted"></i> Latitude</th>
                                    <th><i class="fas fa-globe text-muted"></i> Longitude</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($route->optimized_waypoints as $index => $waypoint)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $waypoint->name ?? 'Unknown Location' }}</strong>
                                    </td>
                                    <td>
                                        <code>{{ $waypoint->lat ?? 'N/A' }}</code>
                                    </td>
                                    <td>
                                        <code>{{ $waypoint->lng ?? 'N/A' }}</code>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-route fa-4x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-800">No Waypoints Defined</h5>
                        <p class="text-muted">This route doesn't have any waypoints configured yet.</p>
                        <a href="{{ route('route-management.edit', $route) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Waypoints
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card border-left-warning shadow">
        <div class="card-header py-3 bg-warning text-white d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-history"></i> Route Logs & Tracking
            </h6>
            <span class="badge bg-light text-dark">
                {{ $route->logs->count() }} Entries
            </span>
        </div>
        <div class="card-body">
            @if($route->logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-clock text-muted"></i> Time</th>
                            <th><i class="fas fa-map-marker text-muted"></i> Location</th>
                            <th><i class="fas fa-tachometer-alt text-muted"></i> Odometer</th>
                            <th><i class="fas fa-car text-muted"></i> Speed</th>
                            <th><i class="fas fa-gas-pump text-muted"></i> Fuel Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($route->logs as $log)
                        <tr>
                            <td>
                                <strong>{{ $log->recorded_at->format('H:i:s') }}</strong>
                                <br><small class="text-muted">{{ $log->recorded_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                {{ $log->location_name ?? 'Unknown' }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $log->odometer }}</span> miles
                            </td>
                            <td>
                                @if($log->speed)
                                    <span class="badge bg-success">{{ $log->speed }}</span> mph
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($log->fuel_level)
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $log->fuel_level > 50 ? 'success' : ($log->fuel_level > 25 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{ $log->fuel_level }}%" aria-valuenow="{{ $log->fuel_level }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $log->fuel_level }}%
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-chart-line fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-gray-800">No Tracking Data</h5>
                <p class="text-muted">No logs have been recorded for this route yet. Tracking data will appear here once the route is active.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapElement = document.getElementById('routeMap');

    try {
        // Get waypoints from PHP as JSON string and parse safely
        const waypointsJson = '{!! addslashes(json_encode($route->optimized_waypoints)) !!}';
        const waypoints = JSON.parse(waypointsJson.replace(/\\/g, ''));

        if (waypoints && Array.isArray(waypoints) && waypoints.length > 0) {
            renderMapPreview(waypoints);
        } else {
            showNoWaypointsMessage();
        }
    } catch (error) {
        console.error('Error loading waypoints:', error);
        showErrorMessage();
    }

    function renderMapPreview(waypoints) {
        const totalDistance = calculateTotalDistance(waypoints);
        mapElement.innerHTML = `
            <div class="text-center text-white p-4">
                <i class="fas fa-route fa-3x mb-3"></i>
                <h6>Route Overview</h6>
                <div class="mb-3">
                    <span class="badge bg-light text-dark me-2">${waypoints.length} Stops</span>
                    <span class="badge bg-light text-dark">${totalDistance.toFixed(1)} miles total</span>
                </div>
                <div class="row text-start">
                    <div class="col-6">
                        <small><strong>Start:</strong></small><br>
                        <span class="small">${waypoints[0]?.name || 'Unknown'}</span>
                    </div>
                    <div class="col-6">
                        <small><strong>End:</strong></small><br>
                        <span class="small">${waypoints[waypoints.length-1]?.name || 'Unknown'}</span>
                    </div>
                </div>
            </div>
        `;
    }

    function calculateTotalDistance(waypoints) {
        // Simple distance calculation (this is approximate)
        if (waypoints.length < 2) return 0;

        let totalDistance = 0;
        for (let i = 1; i < waypoints.length; i++) {
            const prev = waypoints[i-1];
            const current = waypoints[i];
            if (prev.lat && prev.lng && current.lat && current.lng) {
                // Simple distance calculation using Haversine formula approximation
                const latDiff = (current.lat - prev.lat) * Math.PI / 180;
                const lngDiff = (current.lng - prev.lng) * Math.PI / 180;
                const a = Math.sin(latDiff/2) * Math.sin(latDiff/2) +
                         Math.cos(prev.lat * Math.PI / 180) * Math.cos(current.lat * Math.PI / 180) *
                         Math.sin(lngDiff/2) * Math.sin(lngDiff/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                totalDistance += 3959 * c; // Earth radius in miles
            }
        }
        return totalDistance;
    }

    function showNoWaypointsMessage() {
        mapElement.innerHTML = `
            <div class="text-center text-white p-4">
                <i class="fas fa-route fa-3x mb-3"></i>
                <h6>No Waypoints Defined</h6>
                <p class="mb-0 small">This route doesn't have any waypoints configured yet.</p>
            </div>
        `;
    }

    function showErrorMessage() {
        mapElement.innerHTML = `
            <div class="text-center text-white p-4">
                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                <h6>Error Loading Map</h6>
                <p class="mb-0 small">Could not load route waypoints.</p>
            </div>
        `;
    }
});
</script>
@endpush

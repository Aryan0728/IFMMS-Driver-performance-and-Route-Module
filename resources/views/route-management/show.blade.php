@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Route Details: {{ $route->name }}</h1>
            <p class="text-muted">Comprehensive route information and tracking data</p>
        </div>
        <div>
            <a href="{{ route('route-management.index') }}" class="btn btn-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Back to Routes
            </a>
            @if(in_array(auth()->user()->role, ['Admin', 'Technician']))
                <a href="{{ route('route-management.edit', $route) }}" class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-edit"></i> Edit Route
                </a>
                <form method="POST" action="{{ route('route-management.destroy', $route) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this route? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i> Delete Route
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Route Status Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Route Status</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="badge bg-{{ $route->status == 'completed' ? 'success' : ($route->status == 'in_progress' ? 'info' : ($route->status == 'delayed' ? 'warning' : ($route->status == 'canceled' ? 'danger' : 'secondary'))) }} fs-6">
                                    {{ ucfirst(str_replace('_', ' ', $route->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-route fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Distance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($route->actual_distance ?? $route->estimated_distance, 1) }} mi</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-road fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Checkpoints</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $route->checkpoints->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Duration</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $route->actual_duration ?? $route->estimated_duration }} min</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Route Information -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle"></i> Route Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong><i class="fas fa-tag"></i> Route Name:</strong><br>{{ $route->name }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong><i class="fas fa-user"></i> Assigned Driver:</strong><br>{{ $route->driver ? $route->driver->name : 'Unassigned' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong><i class="fas fa-truck"></i> Vehicle:</strong><br>{{ $route->vehicle ? $route->vehicle->make . ' ' . $route->vehicle->model : 'Unassigned' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong><i class="fas fa-calendar"></i> Start Time:</strong><br>{{ $route->start_time ? $route->start_time->format('M d, Y H:i') : 'Not Started' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong><i class="fas fa-clock"></i> Est. End Time:</strong><br>{{ $route->estimated_end_time ? $route->estimated_end_time->format('M d, Y H:i') : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong><i class="fas fa-route"></i> Est. Distance:</strong><br>{{ number_format($route->estimated_distance, 1) }} miles</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong><i class="fas fa-tachometer-alt"></i> Actual Distance:</strong><br>{{ $route->actual_distance ? number_format($route->actual_distance, 1) . ' miles' : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p><strong><i class="fas fa-hourglass-half"></i> Est. Duration:</strong><br>{{ $route->estimated_duration }} minutes</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <p><strong><i class="fas fa-stopwatch"></i> Actual Duration:</strong><br>{{ $route->actual_duration ? $route->actual_duration . ' minutes' : 'N/A' }}</p>
                        </div>
                        <div class="col-sm-12">
                            <p><strong><i class="fas fa-sticky-note"></i> Notes:</strong><br>{{ $route->notes ?? 'No additional notes' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interactive Route Map -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-map-marked-alt"></i> Route Visualization
                    </h6>
                </div>
                <div class="card-body">
                    <div id="routeMap" class="map-container"></div>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Interactive map showing route checkpoints and live vehicle tracking
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Route Checkpoints -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-flag"></i> Route Checkpoints
            </h6>
        </div>
        <div class="card-body">
            @if($route->checkpoints->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-hashtag"></i> Sequence</th>
                                <th><i class="fas fa-map-marker"></i> Checkpoint Name</th>
                                <th><i class="fas fa-address-card"></i> Address</th>
                                <th><i class="fas fa-globe"></i> Coordinates</th>
                                <th><i class="fas fa-tags"></i> Type</th>
                                <th><i class="fas fa-check-circle"></i> Mandatory</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($route->checkpoints->sortBy('sequence_order') as $checkpoint)
                            <tr>
                                <td class="text-center fw-bold">{{ $checkpoint->sequence_order }}</td>
                                <td class="fw-bold">{{ $checkpoint->checkpoint_name }}</td>
                                <td>{{ $checkpoint->address ?? 'N/A' }}</td>
                                <td class="font-monospace small">{{ number_format($checkpoint->latitude, 6) }}, {{ number_format($checkpoint->longitude, 6) }}</td>
                                <td>
                                    <span class="badge bg-{{ $checkpoint->checkpoint_type == 'pickup' ? 'primary' : ($checkpoint->checkpoint_type == 'delivery' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($checkpoint->checkpoint_type) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($checkpoint->is_mandatory)
                                        <i class="fas fa-check text-success"></i>
                                    @else
                                        <i class="fas fa-times text-muted"></i>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Checkpoints Defined</h5>
                    <p class="text-muted">This route does not have any checkpoints configured yet.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Route Logs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-warning text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-history"></i> Route Activity Logs
            </h6>
        </div>
        <div class="card-body">
            @if($route->logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-calendar"></i> Timestamp</th>
                                <th><i class="fas fa-map-pin"></i> Location</th>
                                <th><i class="fas fa-tachometer-alt"></i> Speed (mph)</th>
                                <th><i class="fas fa-gas-pump"></i> Fuel Level (%)</th>
                                <th><i class="fas fa-road"></i> Odometer (mi)</th>
                                <th><i class="fas fa-globe"></i> Coordinates</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($route->logs->take(20) as $log)
                            <tr>
                                <td class="font-monospace small">{{ $log->recorded_at->format('M d, Y H:i:s') }}</td>
                                <td>{{ $log->location_name ?? 'N/A' }}</td>
                                <td class="text-center">{{ number_format($log->speed, 1) }}</td>
                                <td class="text-center">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $log->fuel_level > 75 ? 'success' : ($log->fuel_level > 25 ? 'warning' : 'danger') }}" role="progressbar" style="width: {{ $log->fuel_level }}%" aria-valuenow="{{ $log->fuel_level }}" aria-valuemin="0" aria-valuemax="100">{{ number_format($log->fuel_level, 1) }}%</div>
                                    </div>
                                </td>
                                <td class="text-center">{{ number_format($log->odometer, 1) }}</td>
                                <td class="font-monospace small">{{ number_format($log->latitude, 6) }}, {{ number_format($log->longitude, 6) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($route->logs->count() > 20)
                    <div class="text-center mt-3">
                        <small class="text-muted">Showing latest 20 entries. {{ $route->logs->count() - 20 }} more entries available.</small>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Activity Logs</h5>
                    <p class="text-muted">No GPS tracking data has been recorded for this route yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>



@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="{{ asset('js/echo.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const map = L.map('routeMap').setView([-17.7134, 178.0650], 8);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    const waypoints = @json($route->checkpoints->map(fn($cp) => ['lat' => (float) $cp->latitude, 'lng' => (float) $cp->longitude, 'name' => $cp->checkpoint_name]));
    let markers = [];
    let polyline = null;
    let vehicleMarker = null;

    // Plot checkpoints
    waypoints.forEach((wp, index) => {
        const marker = L.marker([wp.lat, wp.lng])
            .addTo(map)
            .bindPopup(`<b>${wp.name}</b>`);
        markers.push(marker);
    });

    // Plot route polyline
    if (waypoints.length > 1) {
        polyline = L.polyline(waypoints, { color: 'blue' }).addTo(map);
        map.fitBounds(polyline.getBounds().pad(0.1));
    }

    // Fetch initial vehicle position
    @if($route->status === 'in_progress')
        fetch('{{ route('route-management.route-position', $route) }}')
            .then(response => response.json())
            .then(data => {
                if (data.lat && data.lng) {
                    vehicleMarker = L.marker([data.lat, data.lng], {
                        icon: L.icon({
                            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                            iconSize: [25, 41],
                            iconAnchor: [12, 41]
                        })
                    }).addTo(map)
                    .bindPopup(`<b>${data.vehicle || 'Vehicle'}</b><br>Driver: ${data.driver || 'Unassigned'}`);
                    map.panTo([data.lat, data.lng]);
                }
            });

        // WebSocket for live position updates
        Echo.channel('gps-tracking')
            .listen('VehiclePositionUpdated', (e) => {
                if (e.route_id === {{ $route->id }}) {
                    if (vehicleMarker) {
                        vehicleMarker.setLatLng([e.lat, e.lng]);
                        vehicleMarker.setPopupContent(`<b>${e.name}</b><br>Driver: ${e.driver}<br>Lat: ${e.lat.toFixed(6)}<br>Lng: ${e.lng.toFixed(6)}`);
                    } else {
                        vehicleMarker = L.marker([e.lat, e.lng], {
                            icon: L.icon({
                                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                                iconSize: [25, 41],
                                iconAnchor: [12, 41]
                            })
                        }).addTo(map)
                        .bindPopup(`<b>${e.name}</b><br>Driver: ${e.driver}<br>Lat: ${e.lat.toFixed(6)}<br>Lng: ${e.lng.toFixed(6)}`);
                    }
                    map.panTo([e.lat, e.lng]);
                }
            });
    @endif
});
</script>
@endpush
@endsection

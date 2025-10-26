@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <div>
            <a href="{{ route(strtolower(auth()->user()->role).'.dashboard') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 class="h2 d-inline">Route Management</h1>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('route-management.create') }}" class="btn btn-sm btn-primary me-2">
                <i class="fas fa-plus"></i> Create New Route
            </a>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="routeTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="routes-tab" data-bs-toggle="tab" data-bs-target="#routes" type="button" role="tab" aria-controls="routes" aria-selected="true">
                <i class="fas fa-route me-1"></i> Routes
                <span class="badge bg-primary ms-1">{{ $routes->total() }}</span>
            </button>
        </li>
        @if(in_array(auth()->user()->role, ['Admin', 'Technician']))
        <li class="nav-item">
            <button class="nav-link" id="gps-tab" data-bs-toggle="tab" data-bs-target="#gps" type="button" role="tab" aria-controls="gps" aria-selected="false">
                <i class="fas fa-map-marker-alt me-1"></i> GPS Tracking
            </button>
        </li>
        @endif
    </ul>

    <div class="tab-content" id="routeTabsContent">
        <div class="tab-pane fade show active" id="routes" role="tabpanel" aria-labelledby="routes-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-filter"></i> Filter Routes</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('route-management.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Search by route name..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="delayed" {{ request('status') == 'delayed' ? 'selected' : '' }}>Delayed</option>
                                <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="driver_id" class="form-control">
                                <option value="">All Drivers</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col">
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col">
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Apply Filters</button>
                            <a href="{{ route('route-management.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Clear</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-route"></i> Routes</h6>
                </div>
                <div class="card-body">
                    @if($routes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Route Name</th>
                                        <th>Driver</th>
                                        <th>Vehicle</th>
                                        <th>Start Time</th>
                                        <th>Status</th>
                                        <th>Distance (miles)</th>
                                        <th>Duration (min)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($routes as $route)
                                    <tr>
                                        <td>{{ $route->name }}</td>
                                        <td>{{ $route->driver ? $route->driver->name : 'Unassigned' }}</td>
                                        <td>{{ $route->vehicle ? $route->vehicle->make . ' ' . $route->vehicle->model : 'Unassigned' }}</td>
                                        <td>{{ $route->start_time ? $route->start_time->format('M d, Y H:i') : 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $route->status == 'completed' ? 'success' : ($route->status == 'in_progress' ? 'info' : ($route->status == 'delayed' ? 'warning' : ($route->status == 'canceled' ? 'danger' : 'secondary'))) }}">
                                                {{ ucfirst($route->status) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($route->actual_distance ?? $route->estimated_distance, 1) }}</td>
                                        <td>{{ $route->actual_duration ?? $route->estimated_duration }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('route-management.show', $route) }}" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('route-management.edit', $route) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                                <form method="POST" action="{{ route('route-management.destroy', $route) }}" onsubmit="return confirm('Are you sure you want to delete this route?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $routes->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-route fa-4x text-gray-300 mb-3"></i>
                            <h5>No routes found</h5>
                            <p class="text-muted">Create a new route to get started.</p>
                            <a href="{{ route('route-management.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create First Route</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @if(in_array(auth()->user()->role, ['Admin', 'Technician']))
        <div class="tab-pane fade" id="gps" role="tabpanel" aria-labelledby="gps-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-map-marker-alt"></i> GPS Tracking</h6>
                </div>
                <div class="card-body">
                    <div id="gpsMap" style="height: 600px;"></div>
                </div>
            </div>
        </div>
        @endif
    </div>


</div>



@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="{{ asset('js/echo.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(in_array(auth()->user()->role, ['Admin', 'Technician']))
        const map = L.map('gpsMap').setView([-17.7134, 178.0650], 8);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        let vehicleMarkers = {};
        let routePolylines = {};

        // Initial fetch for routes
        function fetchInitialData() {
            fetch('{{ route('route-management.active-routes') }}')
                .then(response => response.json())
                .then(routes => {
                    routes.forEach(route => {
                        if (route.optimized_waypoints) {
                            const latlngs = route.optimized_waypoints.map(wp => [wp.lat, wp.lng]);
                            routePolylines[route.id] = L.polyline(latlngs, { color: 'blue' })
                                .addTo(map)
                                .bindPopup(`<b>${route.name}</b><br>Status: ${route.status}`);
                        }
                    });

                    if (Object.keys(routePolylines).length > 0) {
                        const group = L.featureGroup(Object.values(routePolylines));
                        map.fitBounds(group.getBounds().pad(0.1));
                    }
                });
        }

        fetchInitialData();

        // WebSocket for vehicle position updates
        Echo.channel('gps-tracking')
            .listen('VehiclePositionUpdated', (e) => {
                if (vehicleMarkers[e.id]) {
                    vehicleMarkers[e.id].setLatLng([e.lat, e.lng]);
                    vehicleMarkers[e.id].setPopupContent(`
                        <b>${e.name}</b><br>
                        Driver: ${e.driver}<br>
                        Lat: ${e.lat.toFixed(6)}<br>
                        Lng: ${e.lng.toFixed(6)}
                    `);
                } else {
                    vehicleMarkers[e.id] = L.marker([e.lat, e.lng])
                        .addTo(map)
                        .bindPopup(`
                            <b>${e.name}</b><br>
                            Driver: ${e.driver}<br>
                            Lat: ${e.lat.toFixed(6)}<br>
                            Lng: ${e.lng.toFixed(6)}
                        `);
                }
            });
    @endif
});
</script>
@endpush
@endsection

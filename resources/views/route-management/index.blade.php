 @extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Route Management</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('route-management.create') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-plus"></i> Create New Route
                    </a>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Routes Section -->
                <div id="routes" class="tab-pane fade">
                    <!-- Filters -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Filters & Search</h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('route-management.index') }}" class="row">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label">Search Routes</label>
                                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search by route name...">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned</option>
                                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="delayed" {{ request('status') == 'delayed' ? 'selected' : '' }}>Delayed</option>
                                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
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
                                    <a href="{{ route('route-management.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-sync fa-sm"></i> Clear All
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Routes Table -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">All Routes</h6>
                            <span class="badge bg-primary">Total: {{ $routes->total() }}</span>
                        </div>
                        <div class="card-body">
                            @if($routes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="routesTable" width="100%" cellspacing="0">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Route Name</th>
                                            <th>Driver</th>
                                            <th>Vehicle</th>
                                            <th>Start Time</th>
                                            <th>Status</th>
                                            <th>Distance</th>
                                            <th>Duration</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($routes as $route)
                                        <tr>
                                            <td>
                                                <strong>{{ $route->name }}</strong>
                                                @if($route->notes)
                                                <br><small class="text-muted">{{ Str::limit($route->notes, 30) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-user-circle text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        {{ $route->driver->name }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-truck text-info"></i>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2">
                                                        {{ $route->vehicle->make }} {{ $route->vehicle->model }}
                                                        <br>
                                                        <small class="text-muted">{{ $route->vehicle->license_plate }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-nowrap">{{ $route->start_time->format('M d, Y') }}</span>
                                                <br>
                                                <small class="text-muted">{{ $route->start_time->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @php
                                                    if ($route->status === 'completed') {
                                                        $progress = 100;
                                                        $statusClass = 'success';
                                                        $icon = 'check';
                                                    } elseif ($route->status === 'in_progress') {
                                                        $progress = 60;
                                                        $statusClass = 'primary';
                                                        $icon = 'play-circle';
                                                    } elseif ($route->status === 'planned') {
                                                        $progress = 20;
                                                        $statusClass = 'secondary';
                                                        $icon = 'calendar';
                                                    } elseif ($route->status === 'delayed') {
                                                        $progress = 40;
                                                        $statusClass = 'warning';
                                                        $icon = 'clock';
                                                    } elseif ($route->status === 'canceled') {
                                                        $progress = 0;
                                                        $statusClass = 'danger';
                                                        $icon = 'times-circle';
                                                    } else {
                                                        $progress = 0;
                                                        $statusClass = 'secondary';
                                                        $icon = 'calendar';
                                                    }
                                                @endphp
                                                <div class="mb-2">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar bg-{{ $statusClass }}" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                                <span class="badge bg-{{ $statusClass }}">
                                                    <i class="fas fa-{{ $icon }} fa-sm me-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $route->status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold">{{ $route->estimated_distance }}</span>
                                                <small class="text-muted">miles</small>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold">{{ $route->estimated_duration }}</span>
                                                <small class="text-muted">min</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('route-management.show', $route) }}" class="btn btn-info btn-sm" title="View Details">
                                                        <i class="fas fa-eye fa-sm"></i>
                                                    </a>
                                                    <a href="{{ route('route-management.edit', $route) }}" class="btn btn-warning btn-sm" title="Edit Route">
                                                        <i class="fas fa-edit fa-sm"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('route-management.destroy', $route) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this route?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Route">
                                                            <i class="fas fa-trash fa-sm"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing {{ $routes->firstItem() }} to {{ $routes->lastItem() }} of {{ $routes->total() }} entries
                                </div>
                                <nav>
                                    {{ $routes->links() }}
                                </nav>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="fas fa-route fa-4x text-gray-300 mb-3"></i>
                                <h5 class="text-gray-800">No routes found</h5>
                                <p class="text-muted">Get started by creating your first route.</p>
                                <a href="{{ route('route-management.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus fa-sm"></i> Create First Route
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- GPS Section -->
                <div id="gps" class="tab-pane fade show active">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">GPS Tracking of Vehicles</h6>
                        </div>
                        <div class="card-body">
                            <div id="map" style="height: 400px; width: 100%; overflow: hidden;"></div>
                            <div class="mt-3">
                                <h6>Vehicles List</h6>
                                <ul id="vehicle-list" class="list-group">
                                    <!-- Vehicle items will be dynamically loaded here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
        </main>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar links
    const routesLink = document.querySelector('a[href$="#routes"]');
    const gpsLink = document.querySelector('a[href$="#gps"]');
    const routesTab = document.getElementById('routes');
    const gpsTab = document.getElementById('gps');

    function showTab(targetId) {
        // Hide all tabs
        [routesTab, gpsTab].forEach(tab => {
            tab.classList.remove('show', 'active');
        });

        // Show target tab
        if (targetId === '#routes') {
            routesTab.classList.add('show', 'active');
            if (routesLink) routesLink.closest('li').classList.add('active');
            if (gpsLink) gpsLink.closest('li').classList.remove('active');
        } else if (targetId === '#gps') {
            gpsTab.classList.add('show', 'active');
            if (gpsLink) gpsLink.closest('li').classList.add('active');
            if (routesLink) routesLink.closest('li').classList.remove('active');
        }

        // Trigger map initialization if GPS is shown
        if (targetId === '#gps') {
            setTimeout(initMap, 100);
        }
    }

    // Activate tab based on URL hash on page load
    const hash = window.location.hash;
    if (hash === '#routes' || hash === '#gps') {
        showTab(hash);
    } else {
        // Default to GPS tab
        showTab('#gps');
    }

    // Click handlers for sidebar links
    if (routesLink) {
        routesLink.addEventListener('click', function(e) {
            e.preventDefault();
            showTab('#routes');
            history.pushState(null, null, '#routes');
        });
    }

    if (gpsLink) {
        gpsLink.addEventListener('click', function(e) {
            e.preventDefault();
            showTab('#gps');
            history.pushState(null, null, '#gps');
        });
    }

    // Make table rows clickable
    document.querySelectorAll('#routesTable tbody tr').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't trigger if clicking on buttons
            if (!e.target.closest('.btn')) {
                const viewBtn = this.querySelector('a[title="View Details"]');
                if (viewBtn) {
                    window.location.href = viewBtn.href;
                }
            }
        });
    });
    document.querySelectorAll('#routesTable tbody tr').forEach(row => {
        row.style.transition = 'background-color 0.2s ease';
    });

    let map = null;
    let mapInitialized = false;

    // Initialize map for GPS section
    function initMap() {
        const mapElement = document.getElementById('map');
        if (mapElement && !map) {
            // Ensure the map container is visible before initializing
            if (mapElement.offsetParent === null) {
                return; // Not visible yet
            }
            map = L.map('map').setView([-17.7134, 178.0650], 8); // Centered on Fiji
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            mapInitialized = true;
            loadMapData();
        } else if (map && mapInitialized) {
            map.invalidateSize();
        }
    }

    function loadMapData() {
        if (!map) return;

        var markers = {};
        var routeLines = [];

        function updateVehicleList(vehicles) {
            var list = document.getElementById('vehicle-list');
            if (!list) return;
            list.innerHTML = '';
            vehicles.forEach(function(vehicle) {
                var li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                    <span>${vehicle.name} - Driver: ${vehicle.driver}</span>
                    <small class="text-muted">(Lat: ${vehicle.lat.toFixed(4)}, Lng: ${vehicle.lng.toFixed(4)})</small>
                `;
                list.appendChild(li);
            });
        }

        function plotVehicles(vehicles) {
            // Clear existing markers
            Object.values(markers).forEach(marker => map.removeLayer(marker));
            markers = {};

            vehicles.forEach(function(vehicle) {
                var marker = L.marker([vehicle.lat, vehicle.lng]).addTo(map)
                    .bindPopup(`<b>${vehicle.name}</b><br>Driver: ${vehicle.driver}`);
                markers[vehicle.id] = marker;
            });
            updateVehicleList(vehicles);
        }

        function plotActiveRoutes(routes) {
            // Clear existing route lines
            routeLines.forEach(line => map.removeLayer(line));
            routeLines = [];

            routes.forEach(function(route) {
                if (route.optimized_waypoints && Array.isArray(route.optimized_waypoints)) {
                    var coords = route.optimized_waypoints.map(wp => [wp.lat, wp.lng]);
                    var polyline = L.polyline(coords, {color: 'blue', weight: 3, opacity: 0.7}).addTo(map)
                        .bindPopup(`<b>${route.name}</b><br>Status: ${route.status}`);
                    routeLines.push(polyline);
                }
            });
        }

        // Fetch and plot vehicles
        fetch('{{ route("route-management.vehicles") }}')
            .then(response => response.json())
            .then(vehicles => {
                plotVehicles(vehicles);
                // Simulate movement every 5 seconds
                setInterval(() => {
                    fetch('{{ route("route-management.vehicles") }}')
                        .then(response => response.json())
                        .then(vehicles => plotVehicles(vehicles));
                }, 5000);
            })
            .catch(error => console.error('Error loading vehicles:', error));

        // Fetch and plot active routes (in_progress or planned)
        fetch('{{ route("route-management.active-routes") }}')
            .then(response => response.json())
            .then(routes => plotActiveRoutes(routes))
            .catch(error => console.error('Error loading routes:', error));
    }

    // Watch for GPS section visibility (for dynamic changes)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const gpsSection = document.getElementById('gps');
                if (gpsSection && gpsSection.classList.contains('show')) {
                    setTimeout(initMap, 100);
                }
            }
        });
    });

    const gpsSection = document.getElementById('gps');
    if (gpsSection) {
        observer.observe(gpsSection, { attributes: true });
        // Initial map init if GPS is active
        if (gpsSection.classList.contains('show')) {
            initMap();
        }
    }
});
</script>
@endpush

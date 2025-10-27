# Route Management Enhancements TODO

## Current Work
Enhancing Route Management functionality based on approved plan: add search, edit/update/delete, GPS with real data, status progress bars.

## Key Technical Concepts
- Laravel routes (web.php), controllers (RouteController), models (Route with relationships to User/Driver and Vehicle), Blade views (index, create, new edit), AJAX for GPS data.
- Eloquent queries with filters/search, validation for updates, JSON responses for API-like endpoints.
- Leaflet map enhancements: Plot DB vehicles (add lat/lng to Vehicle model if needed? Assume simulated for now, or add fields), draw polylines from route waypoints.
- Bootstrap for UI (progress bars, modals for delete confirmation).

## Relevant Files and Code
- routes/web.php: Add routes for edit, update, destroy.
- app/Http/Controllers/RouteManagement/RouteController.php: Add edit(), update(Request $request, Route $route), destroy(Route $route) methods.
- resources/views/route-management/edit.blade.php: New view, copy from create.blade.php, prefill with $route data.
- resources/views/route-management/index.blade.php: Add search form field, update actions column with edit/delete links/forms, add progress bar in status column, update GPS script to fetch real data via AJAX.
- app/Models/Vehicle.php: Ensure has lat/lng or use simulated; for now, keep simulated but label with real vehicles.

## Problem Solving
- Previous seeding issues resolved with migrate:fresh --seed.
- Relationships: Route->driver (User where role='Driver'), Route->vehicle.
- GPS: Add JSON endpoint in controller (e.g., vehicles() method returning Vehicle::with('driver')->get()).

## Pending Tasks and Next Steps
1. Update routes/web.php: Add Route::resource('route-management', RouteController::class)->except(['show']); or specific routes for edit/update/destroy. "As per the most recent conversation, add the routes for edit, update, and destroy to complete the CRUD operations."
2. Update RouteController.php: Implement edit() to return view with prefilled data, update() to validate and save changes, destroy() to soft/hard delete route. "Next, implement the controller methods to handle the new routes."
3. Create edit.blade.php: Form similar to create, with @method('PUT'), prefill inputs from $route. "Create the edit view to allow modifying existing routes."
4. Update index.blade.php: Add search input in filters (name='search'), update controller index to apply ->where('name', 'like', '%'.request('search').'%'), update actions to include edit link and delete form/button, add progress bar (e.g., <div class="progress"><div class="progress-bar" style="width: {{ $progress }}%"></div></div>) based on status (planned:20%, in_progress:50%, etc.), enhance GPS script with fetch('/route-management/vehicles') to get real vehicles, plot markers, and for active routes draw polylines from optimized_waypoints. "Update the index view for search, actions, progress, and real GPS data. Add vehicles() method to controller for JSON."
5. Test: Run php artisan route:list, create/edit/delete routes, verify GPS shows real vehicles/routes on Fiji map. "Finally, test all enhancements and verify functionality."

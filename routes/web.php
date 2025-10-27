<?php
// routes/web.php

use App\Http\Controllers\DriverPerformance\DashboardController;
use App\Http\Controllers\DriverPerformance\DriverController;
use App\Http\Controllers\RouteManagement\RouteController;
use App\Http\Controllers\RouteManagement\LiveRouteController;
use Illuminate\Support\Facades\Route;

// Home Route - Redirect to dashboard
Route::get('/', function () {
    return redirect('/driver-performance');
});

// Driver Performance Routes
Route::prefix('driver-performance')->name('driver-performance.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/driver/{driver}', [DriverController::class, 'show'])->name('show');
    Route::get('/driver/{driver}/metric/{metric}/edit', [DriverController::class, 'edit'])->name('edit');
    Route::put('/driver/{driver}/metric/{metric}', [DriverController::class, 'update'])->name('update');
    Route::post('/driver/{driver}/analyze-behavior', [DriverController::class, 'analyzeBehavior'])->name('analyze-behavior');
});

// Route Management Routes
Route::prefix('route-management')->name('route-management.')->group(function () {
    Route::get('/', [RouteController::class, 'index'])->name('index');
    Route::get('/create', [RouteController::class, 'create'])->name('create');
    Route::post('/', [RouteController::class, 'store'])->name('store');
    Route::get('/{route}', [RouteController::class, 'show'])->name('show');
    Route::get('/{route}/edit', [RouteController::class, 'edit'])->name('edit');
    Route::put('/{route}', [RouteController::class, 'update'])->name('update');
    Route::delete('/{route}', [RouteController::class, 'destroy'])->name('destroy');
    Route::get('/vehicles', [RouteController::class, 'vehicles'])->name('vehicles');
    Route::get('/active-routes', [RouteController::class, 'activeRoutes'])->name('active-routes');
});

// API Routes for live tracking
Route::prefix('api')->group(function () {
    Route::post('/route/{route}/position', [LiveRouteController::class, 'updatePosition']);
    Route::get('/route/{route}/data', [LiveRouteController::class, 'getRouteData']);
});

// Fallback route
Route::fallback(function () {
    return redirect('/driver-performance');
});
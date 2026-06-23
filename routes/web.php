<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseTagController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestStopController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripExportController;
use App\Http\Controllers\TripShareController;
use App\Http\Controllers\TripTemplateController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Vehicles
    Route::resource('vehicles', VehicleController::class)->except(['show']);
    Route::post('vehicles/{vehicle}/set-default', [VehicleController::class, 'setDefault'])
        ->name('vehicles.set-default');

    // Trips
    Route::resource('trips', TripController::class);
    Route::post('trips/{trip}/start', [TripController::class, 'start'])->name('trips.start');
    Route::post('trips/{trip}/complete', [TripController::class, 'complete'])->name('trips.complete');
    Route::post('trips/{trip}/cancel', [TripController::class, 'cancel'])->name('trips.cancel');

    // Waypoints
    Route::post('trips/{trip}/waypoints', [TripController::class, 'addWaypoint'])->name('trips.waypoints.store');
    Route::delete('trips/{trip}/waypoints/{waypointId}', [TripController::class, 'removeWaypoint'])->name('trips.waypoints.destroy');
    Route::put('trips/{trip}/waypoints/reorder', [TripController::class, 'reorderWaypoints'])->name('trips.waypoints.reorder');

    // Save as Template
    Route::post('trips/{trip}/save-template', [TripController::class, 'saveAsTemplate'])->name('trips.save-template');

    // Trip Sharing
    Route::post('trips/{trip}/share', [TripShareController::class, 'share'])->name('trips.share');
    Route::delete('trips/{trip}/share/{share}', [TripShareController::class, 'revoke'])->name('trips.share.revoke');
    Route::put('trips/{trip}/share/{share}', [TripShareController::class, 'updatePermission'])->name('trips.share.update');

    // Trip Export
    Route::get('trips/{trip}/export-pdf', [TripExportController::class, 'exportPdf'])->name('trips.export-pdf');

    // Expenses (nested)
    Route::post('trips/{trip}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::put('trips/{trip}/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('trips/{trip}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('trips/{trip}/expenses/filter', [ExpenseController::class, 'filter'])->name('expenses.filter');

    // Expense Tags (API)
    Route::apiResource('expense-tags', ExpenseTagController::class)->except(['show']);

    // Trip Templates
    Route::resource('templates', TripTemplateController::class)->only(['index', 'store', 'update', 'destroy']);

    // Statistics
    Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics');

    // Map API
    Route::prefix('api/map')->group(function () {
        Route::get('route', [MapController::class, 'getRoute'])->name('map.route');
        Route::get('search', [MapController::class, 'searchPlace'])->name('map.search');
    });

    // Smart Route API
    Route::prefix('api/smart-route')->group(function () {
        Route::post('rest-stops', [RestStopController::class, 'getRestStops'])->name('smart-route.rest-stops');
        Route::post('fuel-stations', [RestStopController::class, 'getFuelStations'])->name('smart-route.fuel-stations');
        Route::post('nearest-fuel', [RestStopController::class, 'getNearestFuelStation'])->name('smart-route.nearest-fuel');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Route model bindings
        Route::model('trip', \App\Models\Trip::class);
        Route::model('vehicle', \App\Models\Vehicle::class);

        // Share common data with all views that use app layout
        View::composer('layouts.app', function ($view) {
            if (auth()->check()) {
                $view->with('sharedTripsCount', auth()->user()->sharedTrips()->count());
            }
        });

        // Register policies
        Gate::policy(\App\Models\Trip::class, \App\Policies\TripPolicy::class);
    }
}

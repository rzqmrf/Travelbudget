<?php

namespace App\Http\Controllers;

use App\Services\BudgetCalculator;
use App\Services\RouteService;
use App\Services\WeatherService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $activeTrip = $user->activeTrip?->load('vehicle', 'expenses');
        $recentTrips = $user->trips()
            ->completed()
            ->with('vehicle', 'expenses')
            ->latest('completed_at')
            ->take(5)
            ->get();

        $totalTrips = $user->trips()->completed()->count();

        $allCompleted = $user->trips()
            ->completed()
            ->withSum('expenses', 'amount')
            ->get();
        $totalSpent = $allCompleted->sum('expenses_sum_amount');

        $monthlyTrips = $user->trips()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->withSum('expenses', 'amount')
            ->get();
        $monthlySpent = $monthlyTrips->sum('expenses_sum_amount');

        $budgetStatus = null;
        $trafficStatus = null;
        $weather = null;

        if ($activeTrip) {
            $budgetStatus = BudgetCalculator::calculateStatus($activeTrip);
            $trafficStatus = RouteService::getTrafficAwareETA($activeTrip);
            $weather = WeatherService::getWeather(
                $activeTrip->destination_lat,
                $activeTrip->destination_lng,
                $activeTrip->destination_name
            );
        }

        $sharedTripsCount = $user->sharedTrips()->count();

        return view('dashboard', compact(
            'activeTrip',
            'recentTrips',
            'totalTrips',
            'totalSpent',
            'monthlySpent',
            'budgetStatus',
            'trafficStatus',
            'weather',
            'sharedTripsCount'
        ));
    }
}

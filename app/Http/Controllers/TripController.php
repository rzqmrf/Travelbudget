<?php

namespace App\Http\Controllers;

use App\Enums\TripStatus;
use App\Http\Requests\StoreTripRequest;
use App\Models\Trip;
use App\Models\TripRoute;
use App\Models\TripTemplate;
use App\Services\BudgetCalculator;
use App\Services\BudgetPredictor;
use App\Services\FuelCalculator;
use App\Services\RouteService;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TripController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $status = $request->get('status');
        $trips = auth()->user()->trips()
            ->with('vehicle', 'expenses')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);

        return view('trips.index', compact('trips', 'status'));
    }

    public function create(Request $request)
    {
        $vehicles = auth()->user()->vehicles()->get();
        $template = null;

        if ($request->has('template')) {
            $template = TripTemplate::where('id', $request->template)
                ->where('user_id', auth()->id())
                ->first();
        }

        $googleMapsApiKey = config('services.google.maps_api_key');

        return view('trips.create', compact('vehicles', 'template', 'googleMapsApiKey'));
    }

    public function store(StoreTripRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status'] = TripStatus::Planning;

        // Extract waypoints
        $waypointsData = $request->input('waypoints', []);
        unset($data['waypoints']);

        // Extract routes data from request if present (JSON or array format)
        $routesData = $request->input('routes', []);
        if (is_string($routesData)) {
            $routesData = json_decode($routesData, true) ?? [];
        }
        unset($data['routes']);

        // Determine active geometry and estimates from selected route if provided
        $selectedRouteIndex = null;
        foreach ($routesData as $index => $rData) {
            if (!empty($rData['is_selected'])) {
                $selectedRouteIndex = $index;
                break;
            }
        }

        // If none is selected but routes exist, select the first one
        if ($selectedRouteIndex === null && !empty($routesData)) {
            $routesData[0]['is_selected'] = true;
            $selectedRouteIndex = 0;
        }

        if ($selectedRouteIndex !== null) {
            $baseDistance  = $routesData[$selectedRouteIndex]['distance_km'];
            $baseDuration  = $routesData[$selectedRouteIndex]['duration_minutes'];
            $baseFuelCost  = $routesData[$selectedRouteIndex]['estimated_fuel_cost'];

            // Double values for round trip
            $multiplier = !empty($data['is_round_trip']) ? 2 : 1;

            $data['distance_km']          = $baseDistance * $multiplier;
            $data['duration_minutes']     = $baseDuration * $multiplier;
            $data['estimated_fuel_cost']  = $baseFuelCost * $multiplier;
            $data['route_geometry']       = $routesData[$selectedRouteIndex]['geometry'] ?? null;
        }

        $trip = Trip::create($data);

        // Save routes
        foreach ($routesData as $rData) {
            $trip->routes()->create([
                'route_name'          => $rData['route_name'],
                'distance_km'         => $rData['distance_km'],
                'duration_minutes'    => $rData['duration_minutes'],
                'estimated_fuel_cost' => $rData['estimated_fuel_cost'],
                'route_geometry'      => $rData['geometry'] ?? null,
                'is_selected'         => $rData['is_selected'] ?? false,
                'route_summary'       => $rData['summary'] ?? null,
            ]);
        }

        // Save waypoints
        foreach ($waypointsData as $index => $wpData) {
            $trip->waypoints()->create([
                'name'                   => $wpData['name'],
                'latitude'               => $wpData['latitude'],
                'longitude'              => $wpData['longitude'],
                'order_index'            => $wpData['order_index'] ?? $index,
                'stay_duration_minutes'  => $wpData['stay_duration_minutes'] ?? 0,
                'notes'                  => $wpData['notes'] ?? null,
            ]);
        }

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Trip berhasil dibuat!');
    }

    public function show(Trip $trip)
    {
        $this->authorize('view', $trip);

        $trip->load('vehicle', 'expenses.tags', 'routes', 'selectedRoute', 'waypoints', 'shares.sharedWithUser');
        $budgetStatus = BudgetCalculator::calculateStatus($trip);
        $prediction = BudgetPredictor::predict($trip);

        $expensesByCategory = $trip->expenses
            ->groupBy(fn($item) => $item->category->value)
            ->map(fn($items) => $items->sum('amount'));

        $userTags = auth()->user()->expenseTags()->get();
        $trafficStatus = $trip->status->value === 'active' ? RouteService::getTrafficAwareETA($trip) : null;
        $weather = WeatherService::getWeather($trip->destination_lat, $trip->destination_lng, $trip->destination_name);

        $googleMapsApiKey = config('services.google.maps_api_key');

        return view('trips.show', compact('trip', 'budgetStatus', 'prediction', 'expensesByCategory', 'userTags', 'trafficStatus', 'weather', 'googleMapsApiKey'));
    }

    public function edit(Trip $trip)
    {
        $this->authorize('update', $trip);
        $vehicles = auth()->user()->vehicles()->get();
        return view('trips.edit', compact('trip', 'vehicles'));
    }

    public function update(StoreTripRequest $request, Trip $trip)
    {
        $this->authorize('update', $trip);
        $data = $request->validated();

        // Recalculate for round trip if status changed
        // We keep existing geometry; just update budget-related fields
        $trip->update($data);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Trip berhasil diupdate!');
    }

    public function destroy(Trip $trip)
    {
        $this->authorize('delete', $trip);
        $trip->delete();

        return redirect()->route('trips.index')
            ->with('success', 'Trip berhasil dihapus!');
    }

    public function start(Trip $trip)
    {
        $this->authorize('update', $trip);
        $trip->update([
            'status'     => TripStatus::Active,
            'started_at' => now(),
        ]);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Trip dimulai! Selamat perjalanan! 🚗');
    }

    public function complete(Trip $trip)
    {
        $this->authorize('update', $trip);
        $trip->update([
            'status'       => TripStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Trip selesai! Terima kasih sudah menggunakan TravelBudget! 🎉');
    }

    public function cancel(Trip $trip)
    {
        $this->authorize('update', $trip);
        $trip->update([
            'status' => TripStatus::Cancelled,
        ]);

        return redirect()->route('trips.index')
            ->with('success', 'Trip dibatalkan.');
    }

    public function addWaypoint(Request $request, Trip $trip)
    {
        $this->authorize('update', $trip);

        $data = $request->validate([
            'name'                   => 'required|string|max:255',
            'latitude'               => 'required|numeric',
            'longitude'              => 'required|numeric',
            'stay_duration_minutes'  => 'nullable|integer|min:0',
            'notes'                  => 'nullable|string',
        ]);

        $maxOrder = $trip->waypoints()->max('order_index') ?? -1;
        $data['order_index'] = $maxOrder + 1;

        $trip->waypoints()->create($data);

        // Recalculate route to include new waypoint
        $this->recalculateRoute($trip);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Waypoint berhasil ditambahkan!');
    }

    public function removeWaypoint(Trip $trip, $waypointId)
    {
        $this->authorize('update', $trip);
        $trip->waypoints()->where('id', $waypointId)->delete();

        // Recalculate route after removing waypoint
        $this->recalculateRoute($trip);

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Waypoint berhasil dihapus!');
    }

    public function reorderWaypoints(Request $request, Trip $trip)
    {
        $this->authorize('update', $trip);

        $data = $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:trip_waypoints,id',
        ]);

        foreach ($data['order'] as $index => $waypointId) {
            $trip->waypoints()->where('id', $waypointId)->update(['order_index' => $index]);
        }

        // Recalculate route after reordering
        $this->recalculateRoute($trip);

        return response()->json(['success' => true]);
    }

    public function saveAsTemplate(Trip $trip)
    {
        $this->authorize('view', $trip);

        $template = TripTemplate::create([
            'user_id'               => auth()->id(),
            'name'                  => $trip->name . ' (Template)',
            'origin_name'           => $trip->origin_name,
            'origin_lat'            => $trip->origin_lat,
            'origin_lng'            => $trip->origin_lng,
            'destination_name'      => $trip->destination_name,
            'destination_lat'       => $trip->destination_lat,
            'destination_lng'       => $trip->destination_lng,
            'default_budget'        => $trip->budget_amount,
            'default_vehicle_type'  => $trip->vehicle?->type?->value,
            'waypoints_json'        => $trip->waypoints->map(fn($wp) => [
                'name'                  => $wp->name,
                'latitude'              => $wp->latitude,
                'longitude'             => $wp->longitude,
                'stay_duration_minutes' => $wp->stay_duration_minutes,
            ])->toArray(),
            'notes' => $trip->notes,
        ]);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil disimpan!');
    }

    /**
     * Recalculate the trip route geometry and estimates through all waypoints.
     */
    private function recalculateRoute(Trip $trip): void
    {
        $trip->refresh();
        $trip->load('waypoints');

        // Build coordinate list: origin -> waypoints -> destination
        $coordinates = [[$trip->origin_lat, $trip->origin_lng]];

        foreach ($trip->waypoints->sortBy('order_index') as $wp) {
            $coordinates[] = [(float) $wp->latitude, (float) $wp->longitude];
        }

        $coordinates[] = [$trip->destination_lat, $trip->destination_lng];

        if (count($coordinates) < 2) return;

        $result = RouteService::getMultiSegmentRoute($coordinates);

        if (empty($result['routes'])) return;

        $best = $result['routes'][0];

        $multiplier = $trip->is_round_trip ? 2 : 1;

        $trip->update([
            'distance_km'         => $best['distance_km'] * $multiplier,
            'duration_minutes'    => $best['duration_minutes'] * $multiplier,
            'route_geometry'      => $best['geometry'],
        ]);

        // Recalculate fuel cost based on vehicle
        if ($trip->vehicle) {
            $fuelCalc = FuelCalculator::calculate(
                $best['distance_km'] * $multiplier,
                $trip->vehicle->fuel_consumption,
                $trip->vehicle->fuel_price
            );
            $trip->update(['estimated_fuel_cost' => $fuelCalc['fuel_cost']]);
        }
    }
}

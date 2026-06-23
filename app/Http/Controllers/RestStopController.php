<?php

namespace App\Http\Controllers;

use App\Services\RestStopService;
use App\Services\FuelStationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestStopController extends Controller
{
    public function getRestStops(Request $request): JsonResponse
    {
        $request->validate([
            'route_coords' => 'required|array|min:2',
            'route_coords.*' => 'required|array|size:2',
            'categories' => 'nullable|array',
            'radius' => 'nullable|integer|min:500|max:10000',
        ]);

        $coords = $request->route_coords;
        $categories = $request->categories ?? ['rest_area', 'restaurant', 'cafe', 'place_of_worship', 'toilet'];
        $radius = $request->radius ?? 2000;

        $stops = RestStopService::findAlongRoute($coords, $categories, $radius);

        return response()->json(['stops' => $stops]);
    }

    public function getFuelStations(Request $request): JsonResponse
    {
        $request->validate([
            'route_coords' => 'required|array|min:2',
            'route_coords.*' => 'required|array|size:2',
            'radius' => 'nullable|integer|min:500|max:10000',
        ]);

        $coords = $request->route_coords;
        $radius = $request->radius ?? 3000;

        $stations = FuelStationService::findAlongRoute($coords, $radius);

        return response()->json(['stations' => $stations]);
    }

    public function getNearestFuelStation(Request $request): JsonResponse
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'radius' => 'nullable|integer|min:500|max:20000',
        ]);

        $station = FuelStationService::findNearest(
            $request->lat,
            $request->lng,
            $request->radius ?? 5000
        );

        return response()->json(['station' => $station]);
    }
}

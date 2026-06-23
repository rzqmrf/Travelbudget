<?php

namespace App\Http\Controllers;

use App\Services\RouteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapController extends Controller
{
    public function getRoute(Request $request): JsonResponse
    {
        $request->validate([
            'origin_lat' => 'required|numeric',
            'origin_lng' => 'required|numeric',
            'dest_lat' => 'required|numeric',
            'dest_lng' => 'required|numeric',
        ]);

        $result = RouteService::getRoutes(
            $request->origin_lat,
            $request->origin_lng,
            $request->dest_lat,
            $request->dest_lng
        );

        return response()->json($result);
    }

    public function searchPlace(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);

        $results = RouteService::searchPlace($request->q);

        return response()->json($results);
    }
}

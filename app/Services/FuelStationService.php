<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FuelStationService
{
    private static string $overpassUrl = 'https://overpass-api.de/api/interpreter';

    /**
     * Find fuel stations along a route.
     */
    public static function findAlongRoute(array $routeCoords, int $radius = 3000): array
    {
        if (empty($routeCoords) || count($routeCoords) < 2) {
            return [];
        }

        $samplePoints = self::sampleRoutePoints($routeCoords, 8);
        $allStations = [];

        foreach ($samplePoints as $point) {
            $lat = $point[0];
            $lng = $point[1];

            $query = "[out:json][timeout:15];
                (
                    node[\"amenity\"=\"fuel\"](around:{$radius},{$lat},{$lng});
                    way[\"amenity\"=\"fuel\"](around:{$radius},{$lat},{$lng});
                );
                out center 15;";

            $response = Http::timeout(20)->post(self::$overpassUrl, ['data' => $query]);

            if ($response->successful()) {
                $data = $response->json();
                foreach ($data['elements'] ?? [] as $element) {
                    $stationLat = $element['lat'] ?? ($element['center']['lat'] ?? null);
                    $stationLng = $element['lon'] ?? ($element['center']['lon'] ?? null);

                    if ($stationLat && $stationLng) {
                        $allStations[] = [
                            'id' => $element['id'],
                            'name' => $element['tags']['name'] ?? $element['tags']['brand'] ?? 'SPBU',
                            'brand' => $element['tags']['brand'] ?? null,
                            'lat' => (float) $stationLat,
                            'lng' => (float) $stationLng,
                            'distance_m' => self::haversineDistance($lat, $lng, $stationLat, $stationLng),
                            'fuel_types' => self::extractFuelTypes($element['tags'] ?? []),
                        ];
                    }
                }
            }
        }

        return collect($allStations)->unique('id')->sortBy('distance_m')->values()->toArray();
    }

    /**
     * Find nearest fuel station from a given point.
     */
    public static function findNearest(float $lat, float $lng, int $radius = 5000): ?array
    {
        $query = "[out:json][timeout:10];
            (
                node[\"amenity\"=\"fuel\"](around:{$radius},{$lat},{$lng});
                way[\"amenity\"=\"fuel\"](around:{$radius},{$lat},{$lng});
            );
            out center 5;";

        $response = Http::timeout(15)->post(self::$overpassUrl, ['data' => $query]);

        if (!$response->successful()) return null;

        $data = $response->json();
        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($data['elements'] ?? [] as $element) {
            $stationLat = $element['lat'] ?? ($element['center']['lat'] ?? null);
            $stationLng = $element['lon'] ?? ($element['center']['lon'] ?? null);

            if ($stationLat && $stationLng) {
                $distance = self::haversineDistance($lat, $lng, $stationLat, $stationLng);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearest = [
                        'name' => $element['tags']['name'] ?? $element['tags']['brand'] ?? 'SPBU',
                        'brand' => $element['tags']['brand'] ?? null,
                        'lat' => (float) $stationLat,
                        'lng' => (float) $stationLng,
                        'distance_m' => round($distance),
                    ];
                }
            }
        }

        return $nearest;
    }

    private static function sampleRoutePoints(array $coords, int $maxPoints): array
    {
        $count = count($coords);
        if ($count <= $maxPoints) return $coords;

        $step = max(1, intdiv($count, $maxPoints));
        $sampled = [];
        for ($i = 0; $i < $count; $i += $step) {
            $sampled[] = $coords[$i];
        }
        return $sampled;
    }

    private static function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c);
    }

    private static function extractFuelTypes(array $tags): array
    {
        $fuelTypes = [];
        $fuelKeys = ['fuel:diesel', 'fuel:octane_90', 'fuel:octane_92', 'fuel:octane_95', 'fuel:octane_98'];
        foreach ($fuelKeys as $key) {
            if (isset($tags[$key]) && $tags[$key] === 'yes') {
                $fuelTypes[] = str_replace('fuel:', '', $key);
            }
        }
        return $fuelTypes;
    }
}

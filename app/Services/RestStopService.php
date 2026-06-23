<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RestStopService
{
    private static string $overpassUrl = 'https://overpass-api.de/api/interpreter';

    /**
     * Find points of interest along a route geometry.
     *
     * @param array $routeCoords Array of [lat, lng] pairs
     * @param array $categories Array of OSM amenity types (restaurant, fuel, mosque, etc.)
     * @param int $radius Search radius in meters from route
     * @return array
     */
    public static function findAlongRoute(array $routeCoords, array $categories = ['rest_area', 'restaurant', 'cafe', 'place_of_worship', 'toilet'], int $radius = 2000): array
    {
        if (empty($routeCoords) || count($routeCoords) < 2) {
            return [];
        }

        // Sample points along the route (every N points to reduce query size)
        $samplePoints = self::sampleRoutePoints($routeCoords, 10);

        $allPois = [];

        foreach ($samplePoints as $point) {
            $lat = $point[0];
            $lng = $point[1];

            $amenityFilter = implode('|', $categories);

            $query = "[out:json][timeout:15];
                (
                    node[\"amenity\"~\"^({$amenityFilter})$\"](around:{$radius},{$lat},{$lng});
                    way[\"amenity\"~\"^({$amenityFilter})$\"](around:{$radius},{$lat},{$lng});
                );
                out center 20;";

            $response = Http::timeout(20)->post(self::$overpassUrl, ['data' => $query]);

            if ($response->successful()) {
                $data = $response->json();
                foreach ($data['elements'] ?? [] as $element) {
                    $poiLat = $element['lat'] ?? ($element['center']['lat'] ?? null);
                    $poiLng = $element['lon'] ?? ($element['center']['lon'] ?? null);

                    if ($poiLat && $poiLng) {
                        $allPois[] = [
                            'id' => $element['id'],
                            'name' => $element['tags']['name'] ?? self::getDefaultName($element['tags']['amenity'] ?? 'unknown'),
                            'type' => $element['tags']['amenity'] ?? 'unknown',
                            'lat' => (float) $poiLat,
                            'lng' => (float) $poiLng,
                            'distance_m' => self::haversineDistance($lat, $lng, $poiLat, $poiLng),
                            'icon' => self::getIconForType($element['tags']['amenity'] ?? 'unknown'),
                        ];
                    }
                }
            }
        }

        // Deduplicate by ID and sort by distance
        $unique = collect($allPois)->unique('id')->sortBy('distance_m')->values()->toArray();

        return $unique;
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
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c);
    }

    private static function getDefaultName(string $type): string
    {
        return match ($type) {
            'rest_area' => 'Area Istirahat',
            'restaurant' => 'Restoran',
            'cafe' => 'Kafe',
            'place_of_worship' => 'Tempat Ibadah',
            'toilet' => 'Toilet Umum',
            'fuel' => 'SPBU',
            default => ucfirst($type),
        };
    }

    private static function getIconForType(string $type): string
    {
        return match ($type) {
            'rest_area' => '🛋️',
            'restaurant' => '🍽️',
            'cafe' => '☕',
            'place_of_worship' => '🕌',
            'toilet' => '🚻',
            'fuel' => '⛽',
            default => '📍',
        };
    }
}

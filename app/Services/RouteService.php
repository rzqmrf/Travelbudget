<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RouteService
{
    private static string $osrmBaseUrl = 'https://router.project-osrm.org';
    private static string $nominatimUrl = 'https://nominatim.openstreetmap.org';
    private static string $googleDirectionsUrl = 'https://maps.googleapis.com/maps/api/directions/json';
    private static string $googlePlacesUrl = 'https://maps.googleapis.com/maps/api/place/textsearch/json';

    public static function getRoutes(float $originLat, float $originLng, float $destLat, float $destLng): array
    {
        $apiKey = config('services.google.maps_api_key');

        if ($apiKey) {
            $response = Http::timeout(30)->get(self::$googleDirectionsUrl, [
                'origin' => "{$originLat},{$originLng}",
                'destination' => "{$destLat},{$destLng}",
                'alternatives' => 'true',
                'language' => 'id',
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $routes = [];

                foreach ($data['routes'] ?? [] as $index => $route) {
                    $distance = 0;
                    $duration = 0;
                    foreach ($route['legs'] ?? [] as $leg) {
                        $distance += $leg['distance']['value'] ?? 0;
                        $duration += $leg['duration']['value'] ?? 0;
                    }

                    $geometry = self::decodePolyline($route['overview_polyline']['points'] ?? '');

                    $routes[] = [
                        'route_name' => 'Rute ' . chr(65 + $index),
                        'distance_km' => round($distance / 1000, 1),
                        'duration_minutes' => round($duration / 60),
                        'geometry' => json_encode($geometry),
                        'summary' => $route['summary'] ?? '',
                    ];
                }

                return ['routes' => $routes];
            }
        }

        // Fallback to OSRM
        $response = Http::timeout(30)->get(self::$osrmBaseUrl . '/route/v1/driving/' .
            "{$originLng},{$originLat};{$destLng},{$destLat}", [
            'overview' => 'full',
            'alternatives' => 'true',
            'geometries' => 'geojson',
            'steps' => 'false',
        ]);

        if (!$response->successful()) {
            return ['routes' => []];
        }

        $data = $response->json();
        $routes = [];

        foreach ($data['routes'] ?? [] as $index => $route) {
            $routes[] = [
                'route_name' => 'Rute ' . chr(65 + $index),
                'distance_km' => round($route['distance'] / 1000, 1),
                'duration_minutes' => round($route['duration'] / 60),
                'geometry' => json_encode($route['geometry']),
                'summary' => $route['legs'][0]['summary'] ?? '',
            ];
        }

        return ['routes' => $routes];
    }

    /**
     * Get multi-segment route through waypoints.
     */
    public static function getMultiSegmentRoute(array $coordinates): array
    {
        if (count($coordinates) < 2) return ['routes' => []];

        $apiKey = config('services.google.maps_api_key');

        if ($apiKey) {
            $origin = "{$coordinates[0][0]},{$coordinates[0][1]}";
            $destIndex = count($coordinates) - 1;
            $destination = "{$coordinates[$destIndex][0]},{$coordinates[$destIndex][1]}";
            
            $waypointsArr = [];
            for ($i = 1; $i < $destIndex; $i++) {
                $waypointsArr[] = "{$coordinates[$i][0]},{$coordinates[$i][1]}";
            }

            $params = [
                'origin' => $origin,
                'destination' => $destination,
                'language' => 'id',
                'key' => $apiKey,
            ];

            if (!empty($waypointsArr)) {
                $params['waypoints'] = implode('|', $waypointsArr);
            }

            $response = Http::timeout(30)->get(self::$googleDirectionsUrl, $params);

            if ($response->successful()) {
                $data = $response->json();
                $routes = [];

                foreach ($data['routes'] ?? [] as $index => $route) {
                    $distance = 0;
                    $duration = 0;
                    foreach ($route['legs'] ?? [] as $leg) {
                        $distance += $leg['distance']['value'] ?? 0;
                        $duration += $leg['duration']['value'] ?? 0;
                    }

                    $geometry = self::decodePolyline($route['overview_polyline']['points'] ?? '');

                    $routes[] = [
                        'route_name' => 'Rute ' . chr(65 + $index),
                        'distance_km' => round($distance / 1000, 1),
                        'duration_minutes' => round($duration / 60),
                        'geometry' => json_encode($geometry),
                        'summary' => $route['summary'] ?? '',
                    ];
                }

                return ['routes' => $routes];
            }
        }

        // Fallback to OSRM
        $coordString = implode(';', array_map(fn($c) => "{$c[1]},{$c[0]}", $coordinates));

        $response = Http::timeout(30)->get(self::$osrmBaseUrl . '/route/v1/driving/' . $coordString, [
            'overview' => 'full',
            'geometries' => 'geojson',
            'steps' => 'false',
        ]);

        if (!$response->successful()) {
            return ['routes' => []];
        }

        $data = $response->json();
        $routes = [];

        foreach ($data['routes'] ?? [] as $index => $route) {
            $routes[] = [
                'route_name' => 'Rute ' . chr(65 + $index),
                'distance_km' => round($route['distance'] / 1000, 1),
                'duration_minutes' => round($route['duration'] / 60),
                'geometry' => json_encode($route['geometry']),
                'summary' => $route['legs'][0]['summary'] ?? '',
            ];
        }

        return ['routes' => $routes];
    }

    /**
     * Estimate traffic-aware ETA for an active trip.
     * Uses time-of-day heuristics when no traffic API is available.
     */
    public static function getTrafficAwareETA(\App\Models\Trip $trip): array
    {
        if (!$trip->started_at || !$trip->duration_minutes) {
            return ['eta' => null, 'traffic_level' => 'unknown', 'remaining_minutes' => null];
        }

        $elapsedMinutes = now()->diffInMinutes($trip->started_at);
        $totalDuration = $trip->duration_minutes;

        // Apply traffic multiplier based on time of day
        $hour = now()->hour;
        $trafficMultiplier = match (true) {
            ($hour >= 7 && $hour <= 9) => 1.4,   // Morning rush
            ($hour >= 16 && $hour <= 19) => 1.5,  // Evening rush
            ($hour >= 11 && $hour <= 13) => 1.2,  // Lunch hour
            ($hour >= 22 || $hour <= 5) => 0.9,   // Late night (faster)
            default => 1.1,                         // Normal
        };

        $adjustedTotalDuration = (int) ($totalDuration * $trafficMultiplier);
        $remainingMinutes = max(0, $adjustedTotalDuration - $elapsedMinutes);

        $trafficLevel = match (true) {
            $trafficMultiplier >= 1.4 => 'heavy',
            $trafficMultiplier >= 1.2 => 'moderate',
            default => 'light',
        };

        $eta = now()->copy()->addMinutes($remainingMinutes);

        return [
            'eta' => $eta->format('H:i'),
            'traffic_level' => $trafficLevel,
            'remaining_minutes' => $remainingMinutes,
            'progress_percent' => min(100, round(($elapsedMinutes / $adjustedTotalDuration) * 100)),
        ];
    }

    public static function searchPlace(string $query): array
    {
        $apiKey = config('services.google.maps_api_key');

        if ($apiKey) {
            $response = Http::timeout(10)->get(self::$googlePlacesUrl, [
                'query' => $query,
                'language' => 'id',
                'key' => $apiKey,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return collect($data['results'] ?? [])->map(fn($place) => [
                    'name' => $place['formatted_address'] ?? $place['name'],
                    'lat' => (float) ($place['geometry']['location']['lat'] ?? 0),
                    'lng' => (float) ($place['geometry']['location']['lng'] ?? 0),
                    'type' => implode(', ', $place['types'] ?? []),
                ])->toArray();
            }
        }

        // Fallback to Nominatim
        $response = Http::timeout(10)
            ->withHeaders(['User-Agent' => 'TravelBudget/1.0'])
            ->get(self::$nominatimUrl . '/search', [
                'q' => $query,
                'format' => 'json',
                'limit' => 5,
                'countrycodes' => 'id',
                'addressdetails' => 1,
            ]);

        if (!$response->successful()) {
            return [];
        }

        return collect($response->json())->map(fn($place) => [
            'name' => $place['display_name'],
            'lat' => (float) $place['lat'],
            'lng' => (float) $place['lon'],
            'type' => $place['type'] ?? '',
        ])->toArray();
    }

    /**
     * Decode Google Maps polyline to GeoJSON LineString format.
     */
    private static function decodePolyline(string $encoded): array
    {
        $length = strlen($encoded);
        $index = 0;
        $lat = 0;
        $lng = 0;
        $coordinates = [];

        while ($index < $length) {
            // Decode Latitude
            $shift = 0;
            $result = 0;
            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lat += $dlat;

            // Decode Longitude
            $shift = 0;
            $result = 0;
            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);
            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;

            // GeoJSON coordinates are [lng, lat]
            $coordinates[] = [round($lng * 1e-5, 6), round($lat * 1e-5, 6)];
        }

        return [
            'type' => 'LineString',
            'coordinates' => $coordinates
        ];
    }
}

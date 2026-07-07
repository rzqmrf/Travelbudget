<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    private static string $apiBaseUrl = 'https://api.openweathermap.org/data/2.5';

    /**
     * Get weather forecast for a location. Cached for 1 hour.
     */
    public static function getWeather(float $lat, float $lng, string $locationName = ''): ?array
    {
        $apiKey = config('services.openweather.key');

        if (!$apiKey) {
            return self::getOpenMeteoWeather($lat, $lng, $locationName);
        }

        $cacheKey = "weather_{$lat}_{$lng}";

        return Cache::remember($cacheKey, 3600, function () use ($lat, $lng, $apiKey, $locationName) {
            try {
                // Current weather
                $currentResponse = Http::timeout(10)->get(self::$apiBaseUrl . '/weather', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'appid' => $apiKey,
                    'units' => 'metric',
                    'lang' => 'id',
                ]);

                // Forecast
                $forecastResponse = Http::timeout(10)->get(self::$apiBaseUrl . '/forecast', [
                    'lat' => $lat,
                    'lon' => $lng,
                    'appid' => $apiKey,
                    'units' => 'metric',
                    'lang' => 'id',
                    'cnt' => 8, // next 24 hours (3-hour intervals)
                ]);

                if (!$currentResponse->successful()) {
                    return self::getOpenMeteoWeather($lat, $lng, $locationName);
                }

                $current = $currentResponse->json();
                $forecast = $forecastResponse->successful() ? $forecastResponse->json() : null;

                $result = [
                    'location' => $locationName ?: ($current['name'] ?? 'Unknown'),
                    'current' => [
                        'temp' => round($current['main']['temp']),
                        'feels_like' => round($current['main']['feels_like']),
                        'description' => $current['weather'][0]['description'] ?? '',
                        'icon' => self::getWeatherIcon($current['weather'][0]['main'] ?? 'Clear'),
                        'humidity' => $current['main']['humidity'] ?? 0,
                        'wind_speed' => round($current['wind']['speed'] ?? 0, 1),
                        'condition' => $current['weather'][0]['main'] ?? 'Clear',
                    ],
                    'forecast' => [],
                    'travel_tip' => '',
                    'tips' => '',
                ];

                if ($forecast && isset($forecast['list'])) {
                    foreach ($forecast['list'] as $item) {
                        $result['forecast'][] = [
                            'time' => date('H:i', $item['dt']),
                            'temp' => round($item['main']['temp']),
                            'description' => $item['weather'][0]['description'] ?? '',
                            'icon' => self::getWeatherIcon($item['weather'][0]['main'] ?? 'Clear'),
                        ];
                    }
                }

                $tip = self::generateTravelTip($result['current']['condition'] ?? 'Clear');
                $result['travel_tip'] = $tip;
                $result['tips'] = $tip;

                return $result;
            } catch (\Exception $e) {
                return self::getOpenMeteoWeather($lat, $lng, $locationName);
            }
        });
    }

    /**
     * Get free weather forecast using Open-Meteo.
     */
    private static function getOpenMeteoWeather(float $lat, float $lng, string $locationName = ''): ?array
    {
        $cacheKey = "weather_openmeteo_{$lat}_{$lng}";

        return Cache::remember($cacheKey, 3600, function () use ($lat, $lng, $locationName) {
            try {
                $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m',
                    'hourly' => 'temperature_2m,weather_code',
                    'forecast_days' => 1
                ]);

                if (!$response->successful()) {
                    return self::getMockWeather($locationName);
                }

                $data = $response->json();
                $current = $data['current'] ?? null;
                if (!$current) {
                    return self::getMockWeather($locationName);
                }

                $mapped = self::mapWmoToCondition((int)$current['weather_code']);

                $result = [
                    'location' => $locationName ?: 'Lokasi',
                    'current' => [
                        'temp' => round($current['temperature_2m']),
                        'feels_like' => round($current['apparent_temperature'] ?? $current['temperature_2m']),
                        'description' => $mapped['description'],
                        'icon' => $mapped['icon'],
                        'humidity' => $current['relative_humidity_2m'] ?? 0,
                        'wind_speed' => round(($current['wind_speed_10m'] ?? 0) / 3.6, 1),
                        'condition' => $mapped['condition'],
                    ],
                    'forecast' => [],
                    'travel_tip' => '',
                    'tips' => '',
                ];

                // Map hourly forecast
                $hourly = $data['hourly'] ?? null;
                if ($hourly && isset($hourly['time'])) {
                    $currentHourIndex = 0;
                    $now = now()->format('Y-m-d\TH:00');

                    foreach ($hourly['time'] as $index => $time) {
                        if ($time >= $now) {
                            $currentHourIndex = $index;
                            break;
                        }
                    }

                    for ($i = 0; $i < 8; $i++) {
                        $idx = $currentHourIndex + $i;
                        if (!isset($hourly['time'][$idx])) break;

                        $timeStr = $hourly['time'][$idx];
                        $timeFormatted = date('H:i', strtotime($timeStr));
                        $wmoCode = (int)$hourly['weather_code'][$idx];
                        $hourMapped = self::mapWmoToCondition($wmoCode);

                        $result['forecast'][] = [
                            'time' => $timeFormatted,
                            'temp' => round($hourly['temperature_2m'][$idx]),
                            'description' => $hourMapped['description'],
                            'icon' => $hourMapped['icon'],
                        ];
                    }
                }

                $tip = self::generateTravelTip($mapped['condition']);
                $result['travel_tip'] = $tip;
                $result['tips'] = $tip;

                return $result;
            } catch (\Exception $e) {
                return self::getMockWeather($locationName);
            }
        });
    }

    private static function mapWmoToCondition(int $code): array
    {
        return match ($code) {
            0 => ['condition' => 'Clear', 'description' => 'Cerah', 'icon' => '☀️'],
            1, 2 => ['condition' => 'Clouds', 'description' => 'Cerah Berawan', 'icon' => '🌤️'],
            3 => ['condition' => 'Clouds', 'description' => 'Berawan', 'icon' => '☁️'],
            45, 48 => ['condition' => 'Fog', 'description' => 'Kabut', 'icon' => '🌫️'],
            51, 53, 55 => ['condition' => 'Drizzle', 'description' => 'Gerimis', 'icon' => '🌧️'],
            61, 63, 65 => ['condition' => 'Rain', 'description' => 'Hujan', 'icon' => '🌧️'],
            71, 73, 75 => ['condition' => 'Snow', 'description' => 'Salju', 'icon' => '❄️'],
            80, 81, 82 => ['condition' => 'Rain', 'description' => 'Hujan Lebat', 'icon' => '🌧️'],
            95, 96, 99 => ['condition' => 'Thunderstorm', 'description' => 'Badai Petir', 'icon' => '⛈️'],
            default => ['condition' => 'Clear', 'description' => 'Cerah', 'icon' => '☀️'],
        };
    }

    private static function getWeatherIcon(string $condition): string
    {
        return match ($condition) {
            'Clear' => '☀️',
            'Clouds' => '☁️',
            'Rain', 'Drizzle' => '🌧️',
            'Thunderstorm' => '⛈️',
            'Snow' => '❄️',
            'Mist', 'Fog', 'Haze' => '🌫️',
            default => '🌤️',
        };
    }

    private static function generateTravelTip(string $condition): string
    {
        return match ($condition) {
            'Rain', 'Drizzle' => 'Hujan diperkirakan. Bawa payung dan hati-hati mengemudi.',
            'Thunderstorm' => 'Badai diperkirakan! Pertimbangkan untuk menunda perjalanan.',
            'Mist', 'Fog', 'Haze' => 'Kabut diperkirakan. Kurangi kecepatan dan nyalakan lampu.',
            'Clear' => 'Cuaca cerah! Perjalanan yang menyenangkan.',
            'Clouds' => 'Berawan tapi aman untuk perjalanan.',
            default => 'Periksa kondisi cuaca terkini sebelum berangkat.',
        };
    }

    private static function getMockWeather(string $locationName): ?array
    {
        return [
            'location' => $locationName ?: 'Lokasi',
            'current' => [
                'temp' => 28,
                'feels_like' => 30,
                'description' => 'Cerah Berawan',
                'icon' => '🌤️',
                'humidity' => 70,
                'wind_speed' => 3.5,
                'condition' => 'Clear',
            ],
            'forecast' => [],
            'travel_tip' => 'Periksa kondisi cuaca terkini sebelum berangkat.',
            'tips' => 'Periksa kondisi cuaca terkini sebelum berangkat.',
        ];
    }
}

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
            return self::getMockWeather($locationName);
        }

        $cacheKey = "weather_{$lat}_{$lng}";

        return Cache::remember($cacheKey, 3600, function () use ($lat, $lng, $apiKey, $locationName) {
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
                return self::getMockWeather($locationName);
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

            $result['travel_tip'] = self::generateTravelTip($result['current']['condition'] ?? 'Clear');

            return $result;
        });
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
                'description' => 'Data cuaca tidak tersedia (API key belum dikonfigurasi)',
                'icon' => '🌤️',
                'humidity' => 70,
                'wind_speed' => 3.5,
                'condition' => 'Clear',
            ],
            'forecast' => [],
            'travel_tip' => 'Periksa kondisi cuaca terkini sebelum berangkat.',
        ];
    }
}

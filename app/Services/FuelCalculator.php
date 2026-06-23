<?php

namespace App\Services;

class FuelCalculator
{
    public static function calculate(float $distanceKm, float $fuelConsumption, float $fuelPrice): array
    {
        if ($fuelConsumption <= 0) {
            throw new \InvalidArgumentException('Konsumsi BBM harus > 0');
        }

        $litersNeeded = $distanceKm / $fuelConsumption;
        $fuelCost = $litersNeeded * $fuelPrice;
        $fuelCost = ceil($fuelCost / 100) * 100;

        return [
            'liters_needed' => round($litersNeeded, 2),
            'fuel_cost' => $fuelCost,
            'distance_km' => $distanceKm,
        ];
    }

    public static function compareRoutes(array $routes, float $fuelConsumption, float $fuelPrice): array
    {
        $results = [];
        foreach ($routes as $index => $route) {
            $calc = self::calculate($route['distance_km'], $fuelConsumption, $fuelPrice);
            $results[] = array_merge($route, $calc, [
                'route_name' => 'Rute ' . chr(65 + $index),
            ]);
        }
        usort($results, fn($a, $b) => $a['fuel_cost'] <=> $b['fuel_cost']);
        if (!empty($results)) {
            $results[0]['is_cheapest'] = true;
        }
        return $results;
    }
}

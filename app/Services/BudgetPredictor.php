<?php

namespace App\Services;

use App\Models\Trip;

class BudgetPredictor
{
    public static function predict(Trip $trip): array
    {
        $budgetStatus = BudgetCalculator::calculateStatus($trip);
        $remaining = $budgetStatus['remaining_budget'];
        $estimatedFuelCost = (float) ($trip->estimated_fuel_cost ?? 0);

        $optimistic = $remaining - $estimatedFuelCost;

        $nonFuelExpenses = (float) $trip->expenses()
            ->where('category', '!=', 'bbm')
            ->sum('amount');
        $distanceTraveled = self::estimateDistanceTraveled($trip);
        $remainingDistance = max(0, ($trip->distance_km ?? 0) - $distanceTraveled);

        $nonFuelRatePerKm = $distanceTraveled > 0
            ? $nonFuelExpenses / $distanceTraveled
            : 0;
        $estimatedAdditionalCost = $nonFuelRatePerKm * $remainingDistance;
        $realistic = $remaining - $estimatedFuelCost - $estimatedAdditionalCost;

        $pessimistic = $realistic - ($estimatedFuelCost * 0.20);

        $isSufficient = $realistic >= 0;

        return [
            'remaining_budget' => $remaining,
            'estimated_fuel_remaining' => $estimatedFuelCost,
            'prediction' => [
                'optimistic' => round($optimistic, 2),
                'realistic' => round($realistic, 2),
                'pessimistic' => round($pessimistic, 2),
            ],
            'is_sufficient' => $isSufficient,
            'suggestion' => self::generateSuggestion($realistic, $remaining),
            'non_fuel_rate_per_km' => round($nonFuelRatePerKm, 2),
            'remaining_distance_km' => round($remainingDistance, 2),
        ];
    }

    private static function estimateDistanceTraveled(Trip $trip): float
    {
        if (!$trip->started_at || !$trip->duration_minutes || $trip->duration_minutes <= 0) {
            return 0;
        }
        $elapsedMinutes = now()->diffInMinutes($trip->started_at);
        $progress = min(1, $elapsedMinutes / $trip->duration_minutes);
        return ($trip->distance_km ?? 0) * $progress;
    }

    private static function generateSuggestion(float $realistic, float $remaining): string
    {
        return match (true) {
            $realistic < 0 =>
                'Budget diperkirakan TIDAK CUKUP. Kurangi pengeluaran atau tambah budget Rp' . number_format(abs($realistic), 0, ',', '.'),
            $realistic < ($remaining * 0.10) =>
                'Budget sangat tipis. Pertimbangkan untuk berhemat.',
            $realistic < ($remaining * 0.30) =>
                'Budget moderat. Masih cukup tetapi perlu berhati-hati.',
            default =>
                'Budget diperkirakan CUKUP sampai tujuan.',
        };
    }
}

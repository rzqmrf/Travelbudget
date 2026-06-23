<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\User;

class BudgetCalculator
{
    public static function calculateStatus(Trip $trip): array
    {
        $budgetAmount = (float) $trip->budget_amount;
        $totalExpenses = (float) $trip->expenses()->sum('amount');
        $remainingBudget = $budgetAmount - $totalExpenses;
        $usagePercent = $budgetAmount > 0
            ? round(($totalExpenses / $budgetAmount) * 100, 1)
            : 0;

        $categoryBreakdown = $trip->expenses()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $status = match (true) {
            $usagePercent >= 100 => 'over_budget',
            $usagePercent >= 80  => 'warning',
            $usagePercent >= 50  => 'moderate',
            default              => 'healthy',
        };

        return [
            'budget_amount' => $budgetAmount,
            'total_expenses' => $totalExpenses,
            'remaining_budget' => $remainingBudget,
            'usage_percent' => $usagePercent,
            'status' => $status,
            'category_breakdown' => $categoryBreakdown,
            'expense_count' => $trip->expenses()->count(),
        ];
    }

    public static function averagePerTrip(User $user): float
    {
        $completedTrips = $user->trips()->completed()->count();
        if ($completedTrips === 0) return 0;
        $totalSpent = $user->trips()
            ->completed()
            ->withSum('expenses', 'amount')
            ->get()
            ->sum('expenses_sum_amount');
        return round($totalSpent / $completedTrips, 2);
    }
}

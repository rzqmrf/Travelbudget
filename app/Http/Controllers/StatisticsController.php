<?php

namespace App\Http\Controllers;

use App\Enums\ExpenseCategory;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $trips = $user->trips()->completed()->with('expenses')->latest('completed_at')->get();

        // Category breakdown
        $categoryData = [];
        foreach (ExpenseCategory::cases() as $cat) {
            $total = $trips->flatMap->expenses->where('category', $cat)->sum('amount');
            if ($total > 0) {
                $categoryData[] = [
                    'label' => $cat->label(),
                    'value' => (float)$total,
                    'color' => $cat->color(),
                ];
            }
        }

        // Per trip breakdown
        $tripData = $trips->take(10)->map(fn($trip) => [
            'label' => $trip->name,
            'budget' => (float) $trip->budget_amount,
            'spent' => (float) $trip->expenses->sum('amount'),
        ])->values();

        // Monthly
        $monthlyData = $user->trips()
            ->completed()
            ->withSum('expenses', 'amount')
            ->get()
            ->groupBy(fn($t) => $t->completed_at?->format('Y-m'))
            ->map(fn($group) => $group->sum('expenses_sum_amount'))
            ->sortKeys()
            ->take(12);

        $formattedMonthlyData = [];
        foreach ($monthlyData as $key => $val) {
            $formattedMonthlyData[] = [
                'label' => $key,
                'value' => (float) $val
            ];
        }

        return view('statistics.index', compact('categoryData', 'tripData', 'formattedMonthlyData', 'trips'));
    }
}

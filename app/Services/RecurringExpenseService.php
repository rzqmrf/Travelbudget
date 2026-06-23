<?php

namespace App\Services;

use App\Enums\TripStatus;
use App\Models\Expense;
use App\Models\Trip;
use Carbon\Carbon;

class RecurringExpenseService
{
    /**
     * Generate recurring expense children for active trips.
     */
    public static function generateForActiveTrips(): int
    {
        $count = 0;
        $activeTrips = Trip::where('status', TripStatus::Active)
            ->whereHas('expenses', fn($q) => $q->where('is_recurring', true))
            ->with(['expenses' => fn($q) => $q->where('is_recurring', true)])
            ->get();

        foreach ($activeTrips as $trip) {
            foreach ($trip->expenses as $expense) {
                if (!$expense->is_recurring || !$expense->recurring_interval) continue;

                $count += self::generateChildren($expense, $trip);
            }
        }

        return $count;
    }

    /**
     * Generate child expenses for a recurring parent expense.
     */
    private static function generateChildren(Expense $expense, Trip $trip): int
    {
        $count = 0;
        $interval = $expense->recurring_interval;

        // Use expense's own spent_at as the base, not the trip's started_at.
        // This prevents generating duplicates for days before the expense was created.
        $baseDate = $expense->spent_at ?? $trip->started_at;
        $tripEnd  = $trip->completed_at ?? now();

        if (!$baseDate) return 0;

        // Calculate how many occurrences should exist since expense was created
        $occurrences     = self::calculateOccurrences($baseDate, $tripEnd, $interval);
        $existingChildren = $expense->childExpenses()->count();

        // Generate missing occurrences
        for ($i = $existingChildren + 1; $i <= $occurrences; $i++) {
            $spentAt = self::calculateDate($baseDate, $i, $interval);

            if ($spentAt->isFuture()) break;
            if ($trip->completed_at && $spentAt->gt($trip->completed_at)) break;

            Expense::create([
                'trip_id'           => $trip->id,
                'category'          => $expense->category->value,
                'amount'            => $expense->amount,
                'note'              => ($expense->note ?: '') . " (Otomatis #{$i})",
                'location_name'     => $expense->location_name,
                'latitude'          => $expense->latitude,
                'longitude'         => $expense->longitude,
                'spent_at'          => $spentAt,
                'is_recurring'      => false,
                'parent_expense_id' => $expense->id,
            ]);

            $count++;
        }

        return $count;
    }

    private static function calculateOccurrences(Carbon $start, Carbon $end, string $interval): int
    {
        return match ($interval) {
            'daily'   => max(0, (int) $start->diffInDays($end)),
            'weekly'  => max(0, (int) floor($start->diffInDays($end) / 7)),
            'monthly' => max(0, (int) $start->diffInMonths($end)),
            default   => 0,
        };
    }

    private static function calculateDate(Carbon $start, int $occurrence, string $interval): Carbon
    {
        return match ($interval) {
            'daily'   => $start->copy()->addDays($occurrence),
            'weekly'  => $start->copy()->addWeeks($occurrence),
            'monthly' => $start->copy()->addMonths($occurrence),
            default   => $start->copy(),
        };
    }
}

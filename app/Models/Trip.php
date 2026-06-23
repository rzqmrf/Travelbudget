<?php

namespace App\Models;

use App\Enums\TripStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Trip extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_id',
        'name',
        'budget_amount',
        'daily_budget_limit',
        'origin_name',
        'origin_lat',
        'origin_lng',
        'destination_name',
        'destination_lat',
        'destination_lng',
        'distance_km',
        'duration_minutes',
        'estimated_fuel_cost',
        'route_geometry',
        'status',
        'started_at',
        'completed_at',
        'notes',
        'is_round_trip',
        'return_date',
    ];

    protected $casts = [
        'status' => TripStatus::class,
        'budget_amount' => 'decimal:2',
        'daily_budget_limit' => 'decimal:2',
        'estimated_fuel_cost' => 'decimal:2',
        'origin_lat' => 'float',
        'origin_lng' => 'float',
        'destination_lat' => 'float',
        'destination_lng' => 'float',
        'distance_km' => 'float',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_round_trip' => 'boolean',
        'return_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(TripRoute::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function selectedRoute(): HasOne
    {
        return $this->hasOne(TripRoute::class)->where('is_selected', true);
    }

    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function getRemainingBudgetAttribute(): float
    {
        return (float) $this->budget_amount - $this->total_expenses;
    }

    public function getBudgetUsagePercentAttribute(): float
    {
        if ((float) $this->budget_amount <= 0) return 0;
        return round(($this->total_expenses / (float) $this->budget_amount) * 100, 1);
    }

    public function getPredictedBudgetAtDestinationAttribute(): float
    {
        return $this->remaining_budget - (float) ($this->estimated_fuel_cost ?? 0);
    }

    public function getIsBudgetSufficientAttribute(): bool
    {
        return $this->predicted_budget_at_destination >= 0;
    }

    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_minutes) return '-';
        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;
        return $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes}m";
    }

    public function scopeActive($query)
    {
        return $query->where('status', TripStatus::Active);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TripStatus::Completed);
    }

    public function waypoints(): HasMany
    {
        return $this->hasMany(TripWaypoint::class)->orderBy('order_index');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(TripShare::class);
    }

    public function sharedWithUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trip_shares', 'trip_id', 'shared_with_user_id')
            ->withPivot('permission', 'shared_at')
            ->withTimestamps();
    }

    public function scopeOfUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeSharedWithUser($query, int $userId)
    {
        return $query->whereHas('shares', function ($q) use ($userId) {
            $q->where('shared_with_user_id', $userId);
        });
    }

    public function getTodayExpensesAttribute(): float
    {
        return (float) $this->expenses()
            ->whereDate('spent_at', today())
            ->sum('amount');
    }

    public function getIsDailyBudgetExceededAttribute(): bool
    {
        if (!$this->daily_budget_limit) return false;
        return $this->today_expenses > (float) $this->daily_budget_limit;
    }
}

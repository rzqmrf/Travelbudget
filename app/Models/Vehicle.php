<?php

namespace App\Models;

use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'fuel_consumption',
        'fuel_price', 'fuel_type', 'is_default',
    ];

    protected $casts = [
        'type' => VehicleType::class,
        'fuel_consumption' => 'float',
        'fuel_price' => 'float',
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function fuelCostForDistance(float $distanceKm): float
    {
        if ($this->fuel_consumption <= 0) return 0;
        $litersNeeded = $distanceKm / $this->fuel_consumption;
        return ceil(($litersNeeded * $this->fuel_price) / 100) * 100;
    }
}

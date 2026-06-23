<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripRoute extends Model
{
    protected $fillable = [
        'trip_id', 'route_name', 'distance_km', 'duration_minutes',
        'estimated_fuel_cost', 'route_geometry', 'is_selected', 'route_summary',
    ];

    protected $casts = [
        'estimated_fuel_cost' => 'decimal:2',
        'distance_km' => 'float',
        'is_selected' => 'boolean',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;
        return $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes}m";
    }
}

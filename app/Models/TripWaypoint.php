<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripWaypoint extends Model
{
    protected $fillable = [
        'trip_id',
        'name',
        'latitude',
        'longitude',
        'order_index',
        'stay_duration_minutes',
        'notes',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'order_index' => 'integer',
        'stay_duration_minutes' => 'integer',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function getFormattedStayDurationAttribute(): string
    {
        if (!$this->stay_duration_minutes) return '-';
        $hours = intdiv($this->stay_duration_minutes, 60);
        $minutes = $this->stay_duration_minutes % 60;
        return $hours > 0 ? "{$hours}j {$minutes}m" : "{$minutes}m";
    }
}

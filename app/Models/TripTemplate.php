<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'origin_name',
        'origin_lat',
        'origin_lng',
        'destination_name',
        'destination_lat',
        'destination_lng',
        'default_budget',
        'default_vehicle_type',
        'waypoints_json',
        'notes',
    ];

    protected $casts = [
        'origin_lat' => 'float',
        'origin_lng' => 'float',
        'destination_lat' => 'float',
        'destination_lng' => 'float',
        'default_budget' => 'decimal:2',
        'waypoints_json' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

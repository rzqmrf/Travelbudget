<?php

namespace App\Models;

use App\Enums\SharePermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripShare extends Model
{
    protected $fillable = [
        'trip_id',
        'shared_with_user_id',
        'permission',
        'shared_at',
    ];

    protected $casts = [
        'permission' => SharePermission::class,
        'shared_at' => 'datetime',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function sharedWithUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }
}

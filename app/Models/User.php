<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function defaultVehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class)->where('is_default', true);
    }

    public function activeTrip(): HasOne
    {
        return $this->hasOne(Trip::class)->where('status', 'active')->latest();
    }

    public function tripTemplates(): HasMany
    {
        return $this->hasMany(TripTemplate::class);
    }

    public function expenseTags(): HasMany
    {
        return $this->hasMany(ExpenseTag::class);
    }

    public function sharedTrips(): BelongsToMany
    {
        return $this->belongsToMany(Trip::class, 'trip_shares', 'shared_with_user_id', 'trip_id')
            ->withPivot('permission', 'shared_at')
            ->withTimestamps();
    }
}

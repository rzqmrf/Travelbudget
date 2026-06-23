<?php

namespace App\Policies;

use App\Enums\SharePermission;
use App\Models\Trip;
use App\Models\TripShare;
use App\Models\User;

class TripPolicy
{
    public function view(User $user, Trip $trip): bool
    {
        if ($user->id === $trip->user_id) return true;

        return TripShare::where('trip_id', $trip->id)
            ->where('shared_with_user_id', $user->id)
            ->exists();
    }

    public function update(User $user, Trip $trip): bool
    {
        if ($user->id === $trip->user_id) return true;

        return TripShare::where('trip_id', $trip->id)
            ->where('shared_with_user_id', $user->id)
            ->where('permission', SharePermission::Edit->value)
            ->exists();
    }

    public function delete(User $user, Trip $trip): bool
    {
        return $user->id === $trip->user_id;
    }
}

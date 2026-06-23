<?php

namespace App\Http\Controllers;

use App\Enums\SharePermission;
use App\Models\Trip;
use App\Models\TripShare;
use App\Models\User;
use Illuminate\Http\Request;

class TripShareController extends Controller
{
    public function share(Request $request, Trip $trip)
    {
        if ($trip->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'permission' => 'required|in:view,edit',
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa berbagi dengan diri sendiri.');
        }

        $existing = TripShare::where('trip_id', $trip->id)
            ->where('shared_with_user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->update(['permission' => $data['permission']]);
        } else {
            TripShare::create([
                'trip_id' => $trip->id,
                'shared_with_user_id' => $user->id,
                'permission' => SharePermission::from($data['permission']),
            ]);
        }

        return back()->with('success', "Trip berhasil dibagikan ke {$user->name}!");
    }

    public function revoke(Trip $trip, TripShare $share)
    {
        if ($trip->user_id !== auth()->id()) {
            abort(403);
        }

        $userName = $share->sharedWithUser->name;
        $share->delete();

        return back()->with('success', "Akses {$userName} berhasil dicabut.");
    }

    public function updatePermission(Request $request, Trip $trip, TripShare $share)
    {
        if ($trip->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'permission' => 'required|in:view,edit',
        ]);

        $share->update(['permission' => SharePermission::from($data['permission'])]);

        return back()->with('success', 'Izin akses berhasil diperbarui.');
    }
}

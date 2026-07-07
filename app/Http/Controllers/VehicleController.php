<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $vehicles = $user->vehicles()->withCount('trips')->get();
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(StoreVehicleRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // If this is default, or if there is no vehicle yet, set it default
        $hasVehicles = $user->vehicles()->exists();
        if (empty($hasVehicles)) {
            $data['is_default'] = true;
        } elseif (!empty($data['is_default'])) {
            $user->vehicles()->update(['is_default' => false]);
        }

        Vehicle::create($data);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan!');
    }

    public function edit(Vehicle $vehicle)
    {
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(StoreVehicleRequest $request, Vehicle $vehicle)
    {
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validated();

        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!empty($data['is_default'])) {
            $user->vehicles()->where('id', '!=', $vehicle->id)->update(['is_default' => false]);
        }

        $vehicle->update($data);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil diupdate!');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        if ($vehicle->trips()->exists()) {
            return back()->with('error', 'Kendaraan tidak bisa dihapus karena masih digunakan di trip.');
        }

        $vehicle->delete();

        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Ensure at least one vehicle is default if there are any left
        $firstVehicle = $user->vehicles()->first();
        if ($firstVehicle && !$user->vehicles()->where('is_default', true)->exists()) {
            $firstVehicle->update(['is_default' => true]);
        }

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus!');
    }

    public function setDefault(Vehicle $vehicle)
    {
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        /** @var \App\Models\User $user */
        $user = auth()->user();

        $user->vehicles()->update(['is_default' => false]);
        $vehicle->update(['is_default' => true]);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan default berhasil diubah!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = auth()->user()->vehicles()->withCount('trips')->get();
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

        // If this is default, or if there is no vehicle yet, set it default
        $hasVehicles = auth()->user()->vehicles()->exists();
        if (empty($hasVehicles)) {
            $data['is_default'] = true;
        } elseif (!empty($data['is_default'])) {
            auth()->user()->vehicles()->update(['is_default' => false]);
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

        if (!empty($data['is_default'])) {
            auth()->user()->vehicles()->where('id', '!=', $vehicle->id)->update(['is_default' => false]);
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

        // Ensure at least one vehicle is default if there are any left
        $firstVehicle = auth()->user()->vehicles()->first();
        if ($firstVehicle && !auth()->user()->vehicles()->where('is_default', true)->exists()) {
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

        auth()->user()->vehicles()->update(['is_default' => false]);
        $vehicle->update(['is_default' => true]);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan default berhasil diubah!');
    }
}

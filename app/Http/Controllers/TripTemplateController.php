<?php

namespace App\Http\Controllers;

use App\Models\TripTemplate;
use Illuminate\Http\Request;

class TripTemplateController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $templates = $user->tripTemplates()->latest()->get();
        return view('templates.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'origin_name' => 'required|string|max:255',
            'origin_lat' => 'required|numeric',
            'origin_lng' => 'required|numeric',
            'destination_name' => 'required|string|max:255',
            'destination_lat' => 'required|numeric',
            'destination_lng' => 'required|numeric',
            'default_budget' => 'nullable|numeric|min:0',
            'default_vehicle_type' => 'nullable|string|max:20',
            'waypoints_json' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        TripTemplate::create($data);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil dibuat!');
    }

    public function update(Request $request, TripTemplate $template)
    {
        if ($template->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'default_budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $template->update($data);

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil diupdate!');
    }

    public function destroy(TripTemplate $template)
    {
        if ($template->user_id !== auth()->id()) {
            abort(403);
        }

        $template->delete();

        return redirect()->route('templates.index')
            ->with('success', 'Template berhasil dihapus!');
    }
}

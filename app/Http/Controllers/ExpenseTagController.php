<?php

namespace App\Http\Controllers;

use App\Models\ExpenseTag;
use Illuminate\Http\Request;

class ExpenseTagController extends Controller
{
    public function index()
    {
        $tags = auth()->user()->expenseTags()->latest()->get();
        return response()->json($tags);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:20',
        ]);

        $data['user_id'] = auth()->id();
        $tag = ExpenseTag::create($data);

        return response()->json($tag, 201);
    }

    public function update(Request $request, ExpenseTag $tag)
    {
        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'required|string|max:20',
        ]);

        $tag->update($data);

        return response()->json($tag);
    }

    public function destroy(ExpenseTag $tag)
    {
        if ($tag->user_id !== auth()->id()) {
            abort(403);
        }

        $tag->delete();

        return response()->json(['success' => true]);
    }
}

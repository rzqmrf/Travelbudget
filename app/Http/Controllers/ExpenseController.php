<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Models\Trip;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreExpenseRequest $request, Trip $trip)
    {
        $this->authorize('update', $trip);

        $data = $request->validated();
        $data['spent_at'] = $data['spent_at'] ?? now();

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $data['receipt_path'] = $path;
        }

        unset($data['receipt'], $data['tags']);

        $expense = $trip->expenses()->create($data);

        // Attach tags
        if ($request->has('tags') && is_array($request->tags)) {
            $expense->tags()->sync($request->tags);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengeluaran berhasil ditambahkan!',
                'expense' => $expense
            ]);
        }

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Pengeluaran berhasil ditambahkan!');
    }

    public function update(StoreExpenseRequest $request, Trip $trip, Expense $expense)
    {
        $this->authorize('update', $trip);
        abort_unless($expense->trip_id === $trip->id, 404);

        $data = $request->validated();

        // Handle receipt upload
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $data['receipt_path'] = $path;
        }

        unset($data['receipt'], $data['tags']);

        $expense->update($data);

        // Sync tags
        if ($request->has('tags') && is_array($request->tags)) {
            $expense->tags()->sync($request->tags);
        }

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Pengeluaran berhasil diupdate!');
    }

    public function destroy(Trip $trip, Expense $expense)
    {
        $this->authorize('update', $trip);
        abort_unless($expense->trip_id === $trip->id, 404);

        $expense->delete();

        return redirect()->route('trips.show', $trip)
            ->with('success', 'Pengeluaran berhasil dihapus!');
    }

    public function filter(Request $request, Trip $trip)
    {
        $this->authorize('view', $trip);

        $query = $trip->expenses()->with('tags');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('expense_tags.id', $request->tag));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('spent_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('spent_at', '<=', $request->date_to);
        }
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('note', 'like', "%{$request->search}%")
                    ->orWhere('location_name', 'like', "%{$request->search}%");
            });
        }

        $expenses = $query->orderByDesc('spent_at')->get();

        return response()->json($expenses);
    }
}

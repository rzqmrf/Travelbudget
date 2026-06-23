<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Barryvdh\DomPDF\Facade\Pdf;

class TripExportController extends Controller
{
    public function exportPdf(Trip $trip)
    {
        if ($trip->user_id !== auth()->id()) {
            abort(403);
        }

        $trip->load('vehicle', 'expenses', 'routes', 'waypoints');

        $totalExpenses = $trip->expenses->sum('amount');
        $expensesByCategory = $trip->expenses
            ->groupBy(fn($e) => $e->category->label())
            ->map(fn($items) => $items->sum('amount'));

        $pdf = Pdf::loadView('exports.trip-pdf', compact('trip', 'totalExpenses', 'expensesByCategory'));

        return $pdf->download("trip-{$trip->id}-{$trip->name}.pdf");
    }
}

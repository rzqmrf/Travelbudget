<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'budget_amount' => ['required', 'numeric', 'min:0'],
            'daily_budget_limit' => ['nullable', 'numeric', 'min:0'],
            'origin_name' => ['required', 'string', 'max:255'],
            'origin_lat' => ['required', 'numeric'],
            'origin_lng' => ['required', 'numeric'],
            'destination_name' => ['required', 'string', 'max:255'],
            'destination_lat' => ['required', 'numeric'],
            'destination_lng' => ['required', 'numeric'],
            'distance_km' => ['nullable', 'numeric'],
            'duration_minutes' => ['nullable', 'integer'],
            'estimated_fuel_cost' => ['nullable', 'numeric'],
            'route_geometry' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_round_trip' => ['sometimes', 'boolean'],
            'return_date' => ['nullable', 'date'],
            'routes' => ['nullable'],
            'waypoints' => ['nullable', 'array'],
            'waypoints.*.name' => ['required_with:waypoints', 'string', 'max:255'],
            'waypoints.*.latitude' => ['required_with:waypoints', 'numeric'],
            'waypoints.*.longitude' => ['required_with:waypoints', 'numeric'],
            'waypoints.*.stay_duration_minutes' => ['nullable', 'integer', 'min:0'],
            'waypoints.*.notes' => ['nullable', 'string'],
        ];
    }
}

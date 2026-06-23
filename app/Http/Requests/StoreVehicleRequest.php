<?php

namespace App\Http\Requests;

use App\Enums\VehicleType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(VehicleType::class)],
            'fuel_consumption' => ['required', 'numeric', 'min:0.1'],
            'fuel_price' => ['required', 'numeric', 'min:0'],
            'fuel_type' => ['required', 'string', 'max:50'],
            'is_default' => ['sometimes', 'boolean'],
        ];
    }
}

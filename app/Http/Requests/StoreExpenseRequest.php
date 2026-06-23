<?php

namespace App\Http\Requests;

use App\Enums\ExpenseCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', new Enum(ExpenseCategory::class)],
            'amount' => ['required', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'spent_at' => ['nullable', 'date'],
            'receipt' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_recurring' => ['sometimes', 'boolean'],
            'recurring_interval' => ['nullable', 'string', 'in:daily,weekly,monthly'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:expense_tags,id'],
        ];
    }
}

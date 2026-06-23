<?php

namespace App\Models;

use App\Enums\ExpenseCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    protected $fillable = [
        'trip_id',
        'category',
        'amount',
        'note',
        'latitude',
        'longitude',
        'location_name',
        'spent_at',
        'receipt_path',
        'is_recurring',
        'recurring_interval',
        'parent_expense_id',
    ];

    protected $casts = [
        'category' => ExpenseCategory::class,
        'amount' => 'decimal:2',
        'latitude' => 'float',
        'longitude' => 'float',
        'spent_at' => 'datetime',
        'is_recurring' => 'boolean',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ExpenseTag::class, 'expense_tag');
    }

    public function parentExpense(): BelongsTo
    {
        return $this->belongsTo(Expense::class, 'parent_expense_id');
    }

    public function childExpenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'parent_expense_id');
    }

    public function getReceiptUrlAttribute(): ?string
    {
        return $this->receipt_path ? asset('storage/' . $this->receipt_path) : null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = ['title', 'amount', 'spent_at', 'notes', 'recorded_by'];

    protected $casts = [
        'amount'   => 'decimal:2',
        'spent_at' => 'date',
    ];

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getFormattedAmountAttribute(): string
    {
        return format_currency($this->amount);
    }
}

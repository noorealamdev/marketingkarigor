<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestorPayment extends Model
{
    protected $fillable = ['amount', 'received_at', 'method', 'notes', 'recorded_by'];

    protected $casts = [
        'amount'      => 'decimal:2',
        'received_at' => 'date',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskPayment extends Model
{
    protected $fillable = ['task_id', 'user_id', 'expense_id', 'amount', 'paid_at', 'notes', 'paid_by'];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'date',
    ];

    protected static function booted(): void
    {
        // Keep Finance in sync: removing a payment removes its expense.
        static::deleting(function (TaskPayment $payment) {
            $payment->expense?->delete();
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function getFormattedAmountAttribute(): string
    {
        return format_currency($this->amount);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryPayment extends Model
{
    protected $fillable = [
        'user_id', 'salary_record_id', 'expense_id', 'amount', 'period_month', 'paid_at', 'notes', 'paid_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'period_month' => 'date',
        'paid_at'      => 'date',
    ];

    protected static function booted(): void
    {
        static::deleting(function (SalaryPayment $payment) {
            $payment->expense?->delete();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salaryRecord(): BelongsTo
    {
        return $this->belongsTo(SalaryRecord::class);
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

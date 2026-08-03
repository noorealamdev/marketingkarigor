<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    protected $fillable = ['user_id', 'amount', 'currency', 'period', 'effective_date', 'notes', 'created_by'];

    protected $casts = [
        'amount'         => 'decimal:2',
        'effective_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return format_currency($this->amount);
    }
}

<?php

namespace App\Enums;

use Carbon\Carbon;

enum ReportPeriod: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Daily',
            self::Weekly => 'Weekly',
            self::Monthly => 'Monthly',
        };
    }

    public function endOfPeriod(Carbon $start): Carbon
    {
        return match ($this) {
            self::Daily => $start->copy()->endOfDay(),
            self::Weekly => $start->copy()->addDays(6)->endOfDay(),
            self::Monthly => $start->copy()->endOfMonth(),
        };
    }
}

<?php

namespace App\Models;

use App\Enums\ReportPeriod;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReport extends Model
{
    protected $fillable = [
        'client_id', 'project_id', 'report_type', 'title', 'period_type', 'period_start', 'period_end',
        'metrics', 'summary', 'next_plan', 'notes', 'created_by', 'sent_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'sent_at'      => 'datetime',
        'metrics'      => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function periodEnum(): ReportPeriod
    {
        return ReportPeriod::from($this->period_type);
    }

    public function isSent(): bool
    {
        return $this->sent_at !== null;
    }

    public function tasksCompletedCount(): int
    {
        return Task::whereHas('project', fn ($q) => $q->where('client_id', $this->client_id))
            ->where('status', TaskStatus::Done->value)
            ->whereBetween('updated_at', [$this->period_start->copy()->startOfDay(), $this->period_end->copy()->endOfDay()])
            ->count();
    }
}

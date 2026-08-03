<?php

namespace App\Models;

use App\Enums\NotificationType;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\MemberProfile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia, HasRoles;

    protected $fillable = ['name', 'email', 'password', 'client_id', 'notification_preferences'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'        => 'datetime',
            'password'                 => 'hashed',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Whether this user wants to receive a given notification type on a given channel.
     * Defaults to true (opt-out model) so unset preferences don't silently mute anything.
     */
    public function wantsNotification(NotificationType $type, string $channel): bool
    {
        return (bool) data_get($this->notification_preferences, "{$type->value}.{$channel}", true);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_assignees')->withTimestamps();
    }

    public function uploadedAssets(): HasMany
    {
        return $this->hasMany(ProjectAsset::class, 'uploaded_by');
    }

    public function salaryRecords(): HasMany
    {
        return $this->hasMany(SalaryRecord::class)->orderByDesc('effective_date');
    }

    public function currentSalary(): HasOne
    {
        return $this->hasOne(SalaryRecord::class)->latestOfMany('effective_date');
    }

    public function salaryPayments(): HasMany
    {
        return $this->hasMany(SalaryPayment::class)->orderByDesc('period_month');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(MemberProfile::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(128)->height(128)
            ->nonOptimized()
            ->performOnCollections('avatar');
    }

    public function getRoleNamesAttribute(): string
    {
        return $this->roles->pluck('name')->join(', ') ?: 'No Role';
    }

    public function taskStatsForMonth(int $year, int $month): array
    {
        $base = $this->assignedTasks()->whereYear('updated_at', $year)->whereMonth('updated_at', $month);
        return [
            'completed'   => (clone $base)->where('status', TaskStatus::Done->value)->count(),
            'in_progress' => (clone $base)->whereNotIn('status', [TaskStatus::Todo->value, TaskStatus::Done->value])->count(),
            'todo'        => (clone $base)->where('status', TaskStatus::Todo->value)->count(),
            'total'       => (clone $base)->count(),
        ];
    }
}

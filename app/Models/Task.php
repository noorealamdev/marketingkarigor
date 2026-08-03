<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Task extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name', 'slug', 'description', 'status', 'priority', 'due_date', 'project_id',
        'shared_with_client_at', 'client_approved_at',
    ];

    protected $casts = [
        'due_date'               => 'date',
        'shared_with_client_at'  => 'datetime',
        'client_approved_at'     => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (Task $task) {
            if (empty($task->slug)) {
                $task->slug = static::generateUniqueSlug($task->name);
            }
        });

        static::updating(function (Task $task) {
            if ($task->isDirty('name')) {
                $task->slug = static::generateUniqueSlug($task->name, $task->id);
            }
        });

        static::deleting(function (Task $task) {
            $task->comments->each(fn (TaskComment $comment) => $comment->delete());
        });
    }

    private static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'task';
        $slug = $base;
        $i    = 2;
        while (
            static::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(320)
            ->height(240)
            ->performOnCollections('attachments')
            ->nonQueued();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_assignees')->withTimestamps();
    }

    public function isAssignedTo(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        if ($this->relationLoaded('assignees')) {
            return $this->assignees->contains('id', $user->id);
        }
        return $this->assignees()->whereKey($user->id)->exists();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->with('author')->oldest();
    }

    public function visibleComments(): HasMany
    {
        return $this->comments()->where('visible_to_client', true);
    }

    public function clientApprovalLabel(): ?string
    {
        if ($this->client_approved_at) {
            return 'Approved';
        }
        if ($this->shared_with_client_at) {
            return 'Awaiting Client Approval';
        }
        return null;
    }
}

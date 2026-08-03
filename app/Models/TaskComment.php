<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TaskComment extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['task_id', 'user_id', 'body', 'visible_to_client'];

    protected $casts = [
        'visible_to_client' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('comment_files');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(CommentReaction::class, 'comment_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProjectAsset extends Model
{
    protected $fillable = [
        'project_id', 'name', 'description', 'file_path', 'file_name',
        'file_type', 'file_size', 'category', 'uploaded_by',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ProjectAsset $asset) {
            Storage::disk('public')->delete($asset->file_path);
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->file_type ?? '', 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->file_type ?? '', 'video/');
    }

    public function formattedSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}

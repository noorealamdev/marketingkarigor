<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Client extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name', 'email', 'phone', 'whatsapp', 'facebook_page', 'company', 'address',
        'status', 'notes', 'website', 'brand_colors', 'fonts', 'package', 'renewal_date',
    ];

    protected $casts = [
        'brand_colors'  => 'array',
        'fonts'         => 'array',
        'renewal_date'  => 'date',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('design_references');
        $this->addMediaCollection('brand_guidelines');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(PerformanceReport::class);
    }
}

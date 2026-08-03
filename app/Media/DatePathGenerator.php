<?php

namespace App\Media;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class DatePathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->monthPath($media) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        // Keep conversions namespaced by ID so multiple files' thumbs don't collide
        return $this->monthPath($media) . '/' . $media->id . '-conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->monthPath($media) . '/' . $media->id . '-responsive/';
    }

    protected function monthPath(Media $media): string
    {
        $date = $media->created_at ?? now();
        return $date->year . '/' . $date->month;
    }
}

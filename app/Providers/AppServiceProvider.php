<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;
use App\Support\FlatPathGenerator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PathGenerator::class, FlatPathGenerator::class);
    }

    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols();
        });
    }
}

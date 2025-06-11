<?php

namespace App\Providers;

use App\Filament\Pages\UserProfile;
use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Assets;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Filament::registerPages([
            UserProfile::class,
        ]);
        FilamentAsset::register([
            Assets\Js::make('app', Vite::asset('resources/js/app.js')),
        ]);
    }
}

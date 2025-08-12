<?php

namespace Mvbassalobre\GroqApiService;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GroqApiServiceServiceProvider extends PackageServiceProvider
{
    public string $name = 'groq-api-service';

    public function configurePackage(Package $package): void
    {
        $package->name($this->name)
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasMigrations([
                'create_groq-api-services-settings'
            ]);
    }

    public function bootingPackage()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/groq-api-service.php',
            $this->name
        );

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes([
            __DIR__ . '/settings/GroqApiServiceSettings.php' => app_path('Settings/GroqApiServiceSettings.php'),
            __DIR__ . '/pages/CustomManagerGroqApiServiceSettings.php' => app_path('Filament/Pages/CustomManagerGroqApiServiceSettings.php'),
            __DIR__ . '/database/migrations/create_groq-api-services-settings.php' => database_path('migrations/2025_01_01_000000_create_groq-api-services-settings.php'),
            __DIR__ . '/config/groq-api-service.php' => config_path('groq-api-service.php'),
        ], 'groq-api-service');
    }
}

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
            ->hasConfigFile();
    }

    public function bootingPackage()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/groq-api-service.php',
            $this->name
        );

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/Settings/GroqApiServiceSettings.php' => app_path('Settings/GroqApiServiceSettings.php'),
            __DIR__ . '/Filament/Pages/ManagerGroqApiServiceSettings.php' => app_path('Filament/Pages/ManagerGroqApiServiceSettings.php'),
            __DIR__ . '/../database/migrations/create_groq-api-services-settings.php' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_groq-api-services-settings.php'),
        ]);
    }
}

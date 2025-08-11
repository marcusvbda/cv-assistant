<?php

namespace Mvbassalobre\GrokApiService;

use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GrokApiServiceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'grok-api-service';
    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('marcusvbda/grok-api-service');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentIcon::register($this->getIcons());

        if (app()->runningInConsole()) {
            $publishTag = 'filament-grok-api-service';
            $publishMigration = function ($fileName)  use ($publishTag) {
                if (! $this->migrationFileExists($fileName)) {
                    $this->publishes([
                        __DIR__ . "/database/migrations/{$fileName}" => database_path('migrations/' . date('Y_m_d_His', time()) . '_' . $fileName),
                    ], $publishTag);
                }
            };
            $publishMigration('create_grok-api-services-settings.php');

            $publishSettings = function ($fileName)  use ($publishTag) {
                if (! $this->SettingFileExists($fileName)) {
                    $this->publishes([
                        __DIR__ . "/Settings/{$fileName}" => app_path("/Settings/{$fileName}"),
                    ], $publishTag);
                }
            };
            $publishSettings('GrokApiServiceSettings.php');

            $publicPages = function ($fileName)  use ($publishTag) {
                if (! $this->SettingFileExists($fileName)) {
                    $this->publishes([
                        __DIR__ . "/Filament/Pages/{$fileName}" => app_path("/Filament/Pages/{$fileName}"),
                    ], $publishTag);
                }
            };
            $publicPages('ManagerGrokApiServiceSettings.php');
        }
    }

    protected function getAssetPackageName(): ?string
    {
        return 'mvbassalobre/grok-api-service';
    }

    protected function getAssets(): array
    {
        return [];
    }

    protected function getCommands(): array
    {
        return [];
    }

    protected function getIcons(): array
    {
        return [];
    }

    protected function getRoutes(): array
    {
        return [];
    }

    protected function getScriptData(): array
    {
        return [];
    }

    protected function getMigrations(): array
    {
        return [
            'create_grok-api-services-settings',
        ];
    }

    public static function SettingFileExists(string $settingsFileName): bool
    {
        $len = strlen($settingsFileName);
        foreach (glob(database_path('Settings/*.php')) as $filename) {
            if ((substr($filename, -$len) === $settingsFileName)) {
                return true;
            }
        }

        return false;
    }

    public static function PageExists(string $settingsFileName): bool
    {
        $len = strlen($settingsFileName);
        foreach (glob(database_path('Filament/Pages/*.php')) as $filename) {
            if ((substr($filename, -$len) === $settingsFileName)) {
                return true;
            }
        }

        return false;
    }

    public static function migrationFileExists(string $migrationFileName): bool
    {
        $len = strlen($migrationFileName);
        foreach (glob(database_path('migrations/*.php')) as $filename) {
            if ((substr($filename, -$len) === $migrationFileName)) {
                return true;
            }
        }

        return false;
    }
}

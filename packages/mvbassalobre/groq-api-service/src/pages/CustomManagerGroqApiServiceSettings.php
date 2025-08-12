<?php

namespace App\Filament\Pages;

use Mvbassalobre\GroqApiService\Pages\ManagerGroqApiServiceSettings;

class CustomManagerGroqApiServiceSettings extends ManagerGroqApiServiceSettings
{
    public static function canAccess(): bool
    {
        return true;
    }
}

<?php

namespace App\Filament\Pages;

use marcusvbda\GroqApiService\Pages\ManagerGroqApiServiceSettings as OriginalManagerGroqApiServiceSettings;

class ManagerGroqApiServiceSettings extends OriginalManagerGroqApiServiceSettings
{
    public static function getNavigationGroup(): ?string
    {
        return __('Groq API Service Settings');
    }

    public function getTitle(): string
    {
        return self::getNavigationLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('Settings');
    }

    public static function canAccess(): bool
    {
        return true;
    }
}

<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $ai_provider = "";
    public string $ai_key = "";

    public static function group(): string
    {
        return 'general';
    }
}

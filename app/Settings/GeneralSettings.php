<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public array $ai = [
        'provider' => '',
        'key' => ''
    ];

    public static function group(): string
    {
        return 'general';
    }
}

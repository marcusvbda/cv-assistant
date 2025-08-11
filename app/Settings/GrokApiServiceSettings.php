<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GrokApiServiceSettings extends Settings
{
    public array $settings = [
        'initial_instructions' => '',
        'absolute_rules' => '',
        'expected_response_type' => '',
        'main_context' => '',
    ];

    public static function group(): string
    {
        return 'grok_api';
    }
}

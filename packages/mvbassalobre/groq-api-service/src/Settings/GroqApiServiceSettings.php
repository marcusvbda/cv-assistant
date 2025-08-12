<?php

namespace Mvbassalobre\GroqApiService\Settings;

use Spatie\LaravelSettings\Settings;

class GroqApiServiceSettings extends Settings
{
    public array $settings = [
        'initial_instructions' => '',
        'absolute_rules' => '',
        'expected_response_type' => '',
        'main_context' => '',
    ];

    public static function group(): string
    {
        return 'groq_api';
    }
}

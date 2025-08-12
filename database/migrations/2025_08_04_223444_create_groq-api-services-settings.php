<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if (!$this->migrator->exists('groq_api.settings')) {
            $this->migrator->add('groq_api.settings', [
                'key' => '',
                'bot_name' => 'Groq',
                'temperature' => 0.7,
                'base_url' => 'https://api.groq.com/openai/v1',
                'model' => 'meta-llama/llama-4-maverick-17b-128e-instruct',
                'initial_instructions' => '',
                'absolute_rules' => '',
                'expected_response_type' => '',
                'main_context' => '',
            ]);
        }
    }

    public function down(): void
    {
        if ($this->migrator->exists('groq_api.settings')) {
            $this->migrator->delete('groq_api.settings');
        }
    }
};

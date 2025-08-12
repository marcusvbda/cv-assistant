<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if (!$this->migrator->exists('groq_api.settings')) {
            $this->migrator->add('groq_api.settings', [
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

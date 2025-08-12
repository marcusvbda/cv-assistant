<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('groq_api.settings', [
            'initial_instructions' => '',
            'absolute_rules' => '',
            'expected_response_type' => '',
            'main_context' => '',
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('groq_api.settings');
    }
};

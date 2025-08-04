<?php

use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrator->add('general.ai', [
            'provider' => '',
            'key' => ''
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->migrator->delete('general.ai');
    }
};

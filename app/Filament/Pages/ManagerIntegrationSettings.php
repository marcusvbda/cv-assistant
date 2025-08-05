<?php

namespace App\Filament\Pages;

use App\Settings\IntegrationSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagerIntegrationSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 99;

    protected static string $settings = IntegrationSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __("Settings");
    }

    public function getTitle(): string
    {
        return self::getNavigationLabel();
    }

    public static function getNavigationLabel(): string
    {
        return __('Settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('settings')->tabs([
                    Forms\Components\Tabs\Tab::make(__('AI Integration'))->schema([
                        Forms\Components\Select::make('ai.provider')
                            ->label(__("Provider"))
                            ->options([
                                'groq' => 'GROQ',
                                'openai' => 'OpenAI',
                            ])
                            ->default('groq'),
                        Forms\Components\TextInput::make('ai.key')
                            ->label(__('API key'))
                            ->password()
                            ->revealable()
                    ])->columns(2),
                ])
            ])->columns(1);
    }
}

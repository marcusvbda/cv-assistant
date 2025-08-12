<?php

namespace Mvbassalobre\GroqApiService\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Mvbassalobre\GroqApiService\Settings\GroqApiServiceSettings;

class ManagerGroqApiServiceSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 99;

    protected static string $settings = GroqApiServiceSettings::class;

    public static function getNavigationGroup(): ?string
    {
        return __('Groq API Service');
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
                    Forms\Components\Tabs\Tab::make(__('API Service'))->schema([
                        Forms\Components\Textarea::make('settings.initial_instructions')
                            ->label(__("Initial Instructions"))
                            ->rows(5)
                            ->required(),
                        Forms\Components\Textarea::make('settings.absolute_rules')
                            ->label(__("Absolute Rules"))
                            ->rows(5)
                            ->required(),
                        Forms\Components\Textarea::make('settings.expected_response_type')
                            ->label(__("Response Type"))
                            ->rows(5)
                            ->required(),
                        Forms\Components\Textarea::make('settings.main_context')
                            ->label(__("Main Context"))
                            ->rows(5)
                            ->required(),
                    ]),
                ])
            ])->columns(1);
    }

    public static function canAccess(): bool
    {
        return true;
    }
}
